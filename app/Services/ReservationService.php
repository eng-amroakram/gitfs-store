<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationService extends BaseStockService
{
    public $model = Reservation::class;

    public function __construct()
    {
        $this->model = new Reservation();
    }

    public function model($id)
    {
        return Reservation::find($id);
    }

    public function all($filters = [], $columns = ['*'])
    {
        return Reservation::query()->filters($filters)->select($columns)->get();
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Reservation::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function changeAccountStatus($id)
    {
        return Reservation::changeAccountStatus($id);
    }

    public function delete($id)
    {
        $reservation = $this->model($id);
        if (!$reservation) return false;

        return DB::transaction(function () use ($reservation) {
            // استرجاع المخزون
            foreach ($reservation->items as $item) {
                $this->releaseStock($item->item_id, $item->quantity);
            }
            $reservation->delete();
            return true;
        });
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = Auth::id();
            $reservation = Reservation::store($data);
            $reservation->items()->createMany($data['items']);

            // حجز المخزون
            foreach ($data['items'] as $item) {
                $this->reserveStock($item['item_id'], $item['quantity'], 'reservation');
            }

            return $reservation;
        });
    }

    public function update($data, $id)
    {
        $reservation = $this->model($id);
        if (!$reservation) return false;

        return DB::transaction(function () use ($reservation, $data) {
            // تحرير المخزون القديم
            foreach ($reservation->items as $item) {
                $this->releaseStock($item->item_id, $item->quantity, 'reservation_cancel');
            }

            $reservation->update($data);

            $reservation->items()->delete();
            $reservation->items()->createMany($data['items']);

            // حجز المخزون الجديد
            foreach ($data['items'] as $item) {
                $this->reserveStock($item['item_id'], $item['quantity'], 'reservation');
            }

            return $reservation;
        });
    }

    public function generateReservationNumber()
    {
        return Reservation::generateReservationNumber();
    }
}
