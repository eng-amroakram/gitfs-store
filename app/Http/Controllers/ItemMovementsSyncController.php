<?php

namespace App\Http\Controllers;

use App\Models\ItemMovement;
use App\Services\ItemMovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemMovementsSyncController extends Controller
{
    protected $itemMovementService;

    public function __construct(ItemMovementService $itemMovementService)
    {
        $this->itemMovementService = $itemMovementService;
    }

    // Sync item movements on server to api mobile app
    public function syncItemMovements(Request $request)
    {
        $itemMovements = $this->itemMovementService->allUnsynced(['*']);
        return response()->json([
            'status' => 'success',
            'item_movements' => $itemMovements,
        ]);
    }

    public function confirmSync(Request $request)
    {
        try {
            DB::beginTransaction();

            $ids = $request->input('ids', []); // array of IDs or UUIDs

            if (empty($ids)) {
                return response()->json(['message' => 'No IDs provided'], 400);
            }

            $this->itemMovementService->confirmSync($ids);

            DB::commit();

            return response()->json([
                'message' => 'Item movements synced_at updated successfully',
                'count' => count($ids)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to confirm sync', 'error' => $e->getMessage()], 500);
        }
    }

    public function syncItemMovementsToServer(Request $request)
    {
        $movementsData = $request->input('item_movements', []);
        $syncedIds = [];

        foreach ($movementsData as $movementData) {

            // تحقق من الـ uuid
            $movement = ItemMovement::firstOrNew(['uuid' => $movementData['uuid']]);

            $movement->item_id       = $movementData['item_id'];
            $movement->quantity      = $movementData['quantity'];
            $movement->movement_type = $movementData['movement_type']; // out, reserved, released
            $movement->reason        = $movementData['reason'] ?? null;
            $movement->created_by    = $movementData['created_by'] ?? null;
            $movement->updated_by    = $movementData['updated_by'] ?? null;
            $movement->synced_at     = now();

            $movement->save();

            // تحديث المخزون على أساس حركة المخزون
            // $item = Item::find($movementData['item_id']);
            // if ($item) {
            //     switch ($movementData['movement_type']) {
            //         case 'in':
            //             $item->quantity_total += $movementData['quantity'];
            //             $item->available_quantity += $movementData['quantity'];
            //             break;
            //         case 'out':
            //             $item->quantity_total -= $movementData['quantity'];
            //             $item->available_quantity -= $movementData['quantity'];
            //             break;
            //         case 'reserved':
            //             $item->reserved_quantity += $movementData['quantity'];
            //             $item->available_quantity -= $movementData['quantity'];
            //             break;
            //         case 'released':
            //             $item->reserved_quantity -= $movementData['quantity'];
            //             $item->available_quantity += $movementData['quantity'];
            //             break;
            //     }
            //     $item->save();
            // }

            $syncedIds[] = $movementData['id']; // id من التطبيق
        }

        return response()->json([
            'synced_ids' => $syncedIds,
            'message' => count($syncedIds) . " حركة تمت مزامنتها"
        ]);
    }
}
