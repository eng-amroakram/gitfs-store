<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService extends BaseStockService
{
    public $model = Payment::class;

    public function __construct()
    {
        $this->model = new Payment();
    }

    public function model($id)
    {
        return Payment::find($id);
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Payment::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function store($data, $model = null)
    {
        return DB::transaction(function () use ($model, $data) {
            $payment = $model->payments()->create([
                'payment_reference' => $data['payment_reference'],
                'paymentable_type' => $data['paymentable_type'],
                'paymentable_id'   => $data['paymentable_id'],
                'customer_id'      => $data['customer_id'] ?? null,
                'amount'     => $data['amount'],
                'method'     => $data['method'] ?? 'cash',
                'created_by' => Auth::id(),
            ]);

            $this->updateStatusAndRemaining($model);
            $this->updateCustomerBalance($model);

            return $payment;
        });
    }

    public function update($data, $id, $model = null)
    {
        $payment = Payment::findOrFail($id);

        return DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'amount'     => $data['amount'] ?? $payment->amount,
                'method'     => $data['method'] ?? $payment->method,
                'updated_by' => Auth::id(),
            ]);

            $model = $payment->paymentable;
            $this->updateStatusAndRemaining($model);
            $this->updateCustomerBalance($model);

            return $payment;
        });
    }

    public function delete($id)
    {
        $payment = Payment::findOrFail($id);

        return DB::transaction(function () use ($payment) {
            $model = $payment->paymentable;
            $payment->delete();

            $this->updateStatusAndRemaining($model);
            $this->updateCustomerBalance($model);

            return true;
        });
    }

    /**
     * تحديث حالة الكيان
     */
    protected function updateStatusAndRemaining($model)
    {
        $totalPaid = $model->payments()->sum('amount');

        if ($model instanceof Sale) {
            $totalAmount = $model->grand_total ?? 0;
            $remaining   = max($totalAmount - $totalPaid, 0);
            $model->paid = $totalPaid;
            $model->remaining = $remaining;

            $model->status = match (true) {
                $remaining <= 0           => 'paid',
                $remaining < $totalAmount => 'partial',
                default                   => 'unpaid',
            };
            $model->save();
        }

        if ($model instanceof Reservation) {
            $totalAmount  = $model->total ?? 0;
            $totalPaid   += $model->deposit; // ✅ احتساب العربون
            $remaining    = max($totalAmount - $totalPaid, 0);

            $model->remaining  = $remaining;
            $model->status     = $remaining <= 0 ? 'completed' : 'active';
            $model->save();

            if ($model->status === 'completed' && $remaining <= 0) {
                foreach ($model->items as $resItem) {
                    $this->releaseStock($resItem->item_id, $resItem->quantity, 'reservation_complete');
                }
            }
        }
    }


    /**
     * تحديث رصيد العميل
     */
    protected function updateCustomerBalance($model)
    {
        if (!$model->customer) return;

        $customer = $model->customer;

        // مجموع المبيعات الغير مدفوعة
        $saleBalance = $customer->sales()
            ->with('payments')
            ->get()
            ->sum(function ($sale) {
                $paid = $sale->payments->sum('amount');
                return max($sale->grand_total - $paid, 0);
            });

        // مجموع الحجوزات الغير مدفوعة (مع العربون)
        $reservationBalance = $customer->reservations()
            ->with('payments')
            ->get()
            ->sum(function ($res) {
                $paid = $res->deposit + $res->payments->sum('amount'); // ✅ إضافة العربون
                return max($res->total - $paid, 0);
            });

        $customer->sale_balance        = $saleBalance;
        $customer->reservation_balance = $reservationBalance;
        $customer->total_balance       = $saleBalance + $reservationBalance;

        $customer->save();
    }

    public function generatePaymentReference()
    {
        return Payment::generatePaymentReference();
    }
}
