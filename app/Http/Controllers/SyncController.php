<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\Payment;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SyncController extends Controller
{
    protected $syncService;
    protected $modelsMap;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;

        $this->modelsMap = [
            'users' => User::class,
            'customers' => Customer::class,
            'items' => Item::class,
            'item-movements' => ItemMovement::class,
            'sales' => Sale::class,
            'sale-items' => SaleItem::class,
            'reservations' => Reservation::class,
            'reservation-items' => ReservationItem::class,
            'payments' => Payment::class,
        ];
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * جلب المستخدمين النشطين (مزامنة عامة بدون توثيق)
     */

    public function publicUnUsersSync()
    {
        $records = $this->syncService->getUnsyncedUsers(User::class);

        return response()->json([
            'status' => 'success',
            'entity' => 'users',
            'count' => $records->count(),
            'data' => $records,
        ]);
    }

    public function publicConfirmUsersSync(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        DB::beginTransaction();
        try {
            $count = $this->syncService->confirmUsersSync(User::class, $ids);
            DB::commit();
            return response()->json([
                'message' => "Synced {$count} users",
                'count' => $count
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Sync failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * جلب السجلات الغير مزامنة للمستخدم
     */
    public function sync(Request $request, $entity)
    {
        $user = $request->user();
        if (!isset($this->modelsMap[$entity])) {
            return response()->json(['message' => 'Invalid entity'], 400);
        }

        $modelClass = $this->modelsMap[$entity];
        $records = $this->syncService->getUnsynced($user->id, $modelClass);

        return response()->json([
            'status' => 'success',
            'entity' => $entity,
            'count' => $records->count(),
            'data' => $records,
        ]);
    }

    /**
     * تأكيد المزامنة
     */
    public function confirm(Request $request, $entity)
    {
        $user = $request->user();
        $ids = $request->input('ids', []);

        if (!isset($this->modelsMap[$entity])) {
            return response()->json(['message' => 'Invalid entity'], 400);
        }

        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        $modelClass = $this->modelsMap[$entity];

        DB::beginTransaction();
        try {
            $count = $this->syncService->confirmSync($user->id, $modelClass, $ids);
            DB::commit();
            return response()->json([
                'message' => "Synced {$count} {$entity} records for user {$user->username}",
                'count' => $count
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Sync failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مزامنة كل الجداول دفعة واحدة (من التطبيق المحمول)
     */
    public function syncAll(Request $request)
    {
        $user = $request->user();
        $result = [
            'items' => [],
            'item_movements' => [],
            'sales' => [],
            'sale_items' => [],
            'reservations' => [],
            'reservation_items' => [],
            'payments' => [],
            'message' => ''
        ];

        DB::beginTransaction();
        try {
            // === مزامنة الأصناف ===
            $itemsData = $request->input('items', []);
            foreach ($itemsData as $itemData) {
                $item = Item::firstOrNew(['uuid' => $itemData['uuid']]);
                $item->fill(array_merge($itemData, ['synced_at' => now()]));
                $item->save();
                $this->syncService->logSync($user->id, $item);
                $result['items'][] = $itemData['id'] ?? null;
            }

            // === مزامنة حركات الأصناف ===
            $movementsData = $request->input('item_movements', []);
            foreach ($movementsData as $movementData) {
                $movement = ItemMovement::firstOrNew(['uuid' => $movementData['uuid']]);
                $movement->fill(array_merge($movementData, ['synced_at' => now()]));
                $movement->save();
                $this->syncService->logSync($user->id, $movement);
                $result['item_movements'][] = $movementData['id'] ?? null;
            }

            // === مزامنة المبيعات وبنودها ===
            $salesData = $request->input('sales', []);
            foreach ($salesData as $saleData) {
                $sale = Sale::firstOrNew(['uuid' => $saleData['uuid'] ?? null]);
                $sale->fill(array_merge($saleData, ['synced_at' => now()]));
                $sale->save();
                $this->syncService->logSync($user->id, $sale);
                $result['sales'][] = $saleData['id'] ?? null;

                foreach ($saleData['items'] ?? [] as $itemData) {
                    $saleItem = SaleItem::firstOrNew(['id' => $itemData['id'] ?? null]);
                    $saleItem->fill(array_merge($itemData, ['sale_id' => $sale->id, 'synced_at' => now()]));
                    $saleItem->save();
                    $this->syncService->logSync($user->id, $saleItem);
                    $result['sale_items'][] = $itemData['id'] ?? null;
                }
            }

            // === مزامنة الحجوزات وبنودها ===
            $reservationsData = $request->input('reservations', []);
            foreach ($reservationsData as $reservationData) {
                $reservation = Reservation::firstOrNew(['uuid' => $reservationData['uuid'] ?? null]);
                $reservation->fill(array_merge($reservationData, ['synced_at' => now()]));
                $reservation->save();
                $this->syncService->logSync($user->id, $reservation);
                $result['reservations'][] = $reservationData['id'] ?? null;

                foreach ($reservationData['items'] ?? [] as $itemData) {
                    $reservationItem = ReservationItem::firstOrNew(['id' => $itemData['id'] ?? null]);
                    $reservationItem->fill(array_merge($itemData, ['reservation_id' => $reservation->id, 'synced_at' => now()]));
                    $reservationItem->save();
                    $this->syncService->logSync($user->id, $reservationItem);
                    $result['reservation_items'][] = $itemData['id'] ?? null;
                }
            }

            // === مزامنة الدفعات ===
            $paymentsData = $request->input('payments', []);
            foreach ($paymentsData as $paymentData) {
                $payment = Payment::firstOrNew(['uuid' => $paymentData['uuid'] ?? null]);
                $payment->fill(array_merge($paymentData, ['synced_at' => now()]));
                $payment->save();
                $this->syncService->logSync($user->id, $payment);
                $result['payments'][] = $paymentData['id'] ?? null;
            }

            DB::commit();
            $result['message'] = "تمت مزامنة جميع البيانات بنجاح ✅";

            return response()->json($result);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفع قاعدة بيانات التطبيق المحمول واستبدالها
     */
    public function uploadDatabase(Request $request)
    {
        if ($request->hasFile('database')) {
            $file = $request->file('database');

            if (! $file->isValid()) {
                return response()->json(['message' => 'Uploaded file is not valid.'], 400);
            }

            $destination = storage_path('app');
            $targetPath = $destination . DIRECTORY_SEPARATOR . 'app.db';

            try {
                if (file_exists($targetPath)) {
                    // remove existing file to ensure replacement
                    @unlink($targetPath);
                }

                $file->move($destination, 'app.db');

                return response()->json(['message' => 'Database uploaded and replaced successfully.']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to upload database.', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'No database file found.'], 400);
    }
}
