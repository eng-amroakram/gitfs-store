<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemsSyncController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    // Sync items on server to api mobile app
    public function syncItems(Request $request)
    {
        $items = $this->itemService->allUnsynced(['*']);

        return response()->json([
            'status' => 'success',
            'items' => $items,
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

            $this->itemService->confirmSync($ids);

            DB::commit();

            return response()->json([
                'message' => 'Items synced_at updated successfully',
                'count' => count($ids)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to confirm sync', 'error' => $e->getMessage()], 500);
        }
    }

    public function syncItemsToServer(Request $request)
    {
        $itemsData = $request->input('items', []);

        if (empty($itemsData)) {
            return response()->json([
                'synced_ids' => [],
                'message' => 'لا توجد بيانات للمزامنة'
            ]);
        }

        $syncedIds = [];

        foreach ($itemsData as $itemData) {
            // تحقق من وجود uuid لتحديد إن كان سجل جديد أم موجود
            $item = Item::firstOrNew(['uuid' => $itemData['uuid']]);

            // تحديث أو تعيين البيانات
            $item->name            = $itemData['name'];
            $item->code            = $itemData['code'];
            $item->description     = $itemData['description'] ?? null;
            $item->purchase_price  = $itemData['purchase_price'] ?? 0;
            $item->sale_price      = $itemData['sale_price'] ?? 0;
            $item->quantity_total  = $itemData['quantity_total'] ?? 0;
            $item->reserved_quantity = $itemData['reserved_quantity'] ?? 0;
            $item->available_quantity = $itemData['available_quantity'] ?? 0;
            $item->type            = $itemData['type'] ?? 'sale';
            $item->low_stock_alert = $itemData['low_stock_alert'] ?? 1;
            $item->created_by      = $itemData['created_by'] ?? null;
            $item->updated_by      = $itemData['updated_by'] ?? null;
            $item->synced_at       = now();

            $item->save();
            $syncedIds[] = $itemData['id']; // id من التطبيق المحمول
        }

        return response()->json([
            'synced_ids' => $syncedIds,
            'message' => count($syncedIds) . " سجل تمت مزامنته"
        ]);
    }
}
