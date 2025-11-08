<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationsSyncController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    // Sync reservations on server to api mobile app
    public function syncReservations(Request $request)
    {
        $reservations = $this->reservationService->all(['*']);
        return response()->json([
            'status' => 'success',
            'data' => $reservations,
        ]);
    }

    public function syncReservationsToServer(Request $request)
    {
        $reservationsData = $request->input('reservations', []); // بيانات الحجوزات من التطبيق
        $syncedReservationIds = [];
        $syncedItemIds = [];

        foreach ($reservationsData as $reservationData) {
            // البحث أو إنشاء الحجز حسب uuid
            $reservation = Reservation::firstOrNew(['uuid' => $reservationData['uuid'] ?? null]);

            $reservation->user_id            = $reservationData['user_id'] ?? null;
            $reservation->reservation_number = $reservationData['reservation_number'];
            $reservation->customer_id        = $reservationData['customer_id'] ?? null;
            $reservation->start_date         = $reservationData['start_date'];
            $reservation->end_date           = $reservationData['end_date'];
            $reservation->deposit            = $reservationData['deposit'] ?? 0;
            $reservation->total              = $reservationData['total'];
            $reservation->remaining          = $reservationData['remaining'];
            $reservation->status             = $reservationData['status'];
            $reservation->description        = $reservationData['description'] ?? null;
            $reservation->discount           = $reservationData['discount'] ?? 0;
            $reservation->notes              = $reservationData['notes'] ?? null;
            $reservation->created_by         = $reservationData['created_by'] ?? null;
            $reservation->updated_by         = $reservationData['updated_by'] ?? null;
            $reservation->synced_at          = now();

            $reservation->save();

            // مزامنة البنود مباشرة من items
            $items = $reservationData['items'] ?? [];
            foreach ($items as $itemData) {
                $reservationItem = ReservationItem::firstOrNew(['id' => $itemData['id'] ?? null]);
                $reservationItem->reservation_id = $reservation->id;
                $reservationItem->item_id        = $itemData['item_id'];
                $reservationItem->quantity       = $itemData['quantity'];
                $reservationItem->price          = $itemData['price'];
                $reservationItem->subtotal       = $itemData['subtotal'];
                $reservationItem->status         = $itemData['status'] ?? 'reserved';
                // $reservationItem->created_by     = $itemData['created_by'] ?? null;
                // $reservationItem->updated_by     = $itemData['updated_by'] ?? null;
                $reservationItem->synced_at      = now();
                $reservationItem->save();

                // إضافة ID الخاص بالبند من التطبيق
                $syncedItemIds[] = $itemData['id'] ?? null;
            }

            $syncedReservationIds[] = $reservationData['id']; // id من التطبيق
        }

        return response()->json([
            'synced_reservation_ids' => $syncedReservationIds,
            'synced_item_ids'        => $syncedItemIds,
            'message'    => count($syncedReservationIds) . " حجز تمت مزامنته، " .
                count($syncedItemIds) . " عنصر حجز تمت مزامنته"
        ]);
    }
}
