<?php

namespace App\Livewire\Panel\Payments;

use App\Helpers\LivewireHelper;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditPayment extends Component
{
    use LivewireHelper;

    // بيانات الدفع
    public $payment_reference; // رقم الحجز أو الفاتورة
    public $paymentable_type; // 'sale' أو 'reservation'
    public $paymentable_id;
    public $amount;
    public $method = 'cash';
    public $notes;

    public $allSales = null;
    public $allReservations = null;

    // للسجّل المحدد
    public $selectedRecord = null;

    // الدفعة الحالية
    public $payment;

    public $validation_message = '';

    public function mount()
    {
        $id = session('payment_id');

        if (!$id) {
            $this->alertMessage(__('Payment ID not found in session.'), 'error');
            return redirect()->route('admin.panel.payments.list');
        }

        $this->payment = Payment::findOrFail($id);


        // تعبئة الحقول من الدفعة الحالية
        $this->payment_reference = $this->payment->payment_reference;
        $this->paymentable_type = strtolower(class_basename($this->payment->paymentable_type));
        $this->paymentable_id   = $this->payment->paymentable_id;
        $this->amount           = $this->payment->amount;
        $this->method           = $this->payment->method;
        $this->notes            = $this->payment->notes;

        $saleService = $this->setService('SaleService');
        $resService  = $this->setService('ReservationService');

        // تحميل المبيعات: غير مدفوعة جزئياً أو غير مدفوعة بالكامل
        $this->allSales = $saleService->all(
            ['status' => ['unpaid', 'partial']],
            ['id', 'invoice_number', 'customer_id', 'total', 'discount', 'grand_total', 'status']
        );

        // إضافة الدفعة الحالية إذا لم تكن موجودة
        if ($this->paymentable_type === 'sale' && $this->paymentable_id) {
            $exists = $this->allSales->contains('id', $this->paymentable_id);
            if (!$exists) {
                $currentSale = Sale::find($this->paymentable_id);
                if ($currentSale) $this->allSales->push($currentSale);
            }
        }

        // تحميل الحجوزات: غير مكتملة فقط
        $this->allReservations = $resService->all(
            ['status' => ['active']],
            ['id', 'uuid', 'customer_id', 'deposit', 'remaining', 'total', 'status']
        );

        // إضافة الدفعة الحالية إذا لم تكن موجودة
        if ($this->paymentable_type === 'reservation' && $this->paymentable_id) {
            $exists = $this->allReservations->contains('id', $this->paymentable_id);
            if (!$exists) {
                $currentRes = Reservation::find($this->paymentable_id);
                if ($currentRes) $this->allReservations->push($currentRes);
            }
        }

        // تحميل السجّل المحدد
        $this->loadSelectedRecord();

        // إعادة حساب المدفوع بدون الدفعة الحالية
        if ($this->selectedRecord) {

            $totalPaid = $this->selectedRecord->payments
                ->where('id', '!=', $this->payment->id)
                ->sum('amount');

            if ($this->paymentable_type === 'reservation') {
                $totalPaid += $this->selectedRecord->deposit;
            }

            $this->selectedRecord->paid_amount = $totalPaid + floatval($this->amount);
        }
    }

    #[Layout('layouts.admin.panel'), Title('Edit Payment')]
    public function render()
    {
        return view('livewire.panel.payments.edit-payment', [
            'allSales'        => $this->allSales,
            'allReservations' => $this->allReservations,
            'selectedRecord'  => $this->selectedRecord,
            'payment'         => $this->payment,
        ]);
    }

    public function update()
    {
        try {
            $request = new PaymentRequest();
            $request->merge(['payment_id' => $this->payment->id]);
            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'payment_reference' => $this->payment_reference,
                'paymentable_type' => $this->paymentable_type,
                'paymentable_id'   => $this->paymentable_id,
                'amount'           => $this->amount,
                'method'           => $this->method,
                'notes'            => $this->notes,
            ], $rules, $messages)->validate();

            // تحديد النموذج الصحيح
            $model = null;
            if ($data['paymentable_type'] === 'sale') {
                $model = Sale::with('payments')->find($data['paymentable_id']);
            } elseif ($data['paymentable_type'] === 'reservation') {
                $model = Reservation::with('payments')->find($data['paymentable_id']);
            }

            if (!$model) {
                $this->alertMessage(__('Selected model not found.'), 'error');
                return false;
            }
            $this->validation_message = '';
            // حساب المدفوع بدون الدفعة الحالية
            $totalPaid = $model->payments
                ->where('id', '!=', $this->payment->id)
                ->sum('amount');

            if ($data['paymentable_type'] === 'reservation') {
                $totalPaid += $model->deposit;
            }

            $grandTotal = $model->grand_total ?? $model->total;
            $remaining = $grandTotal - $totalPaid;

            // تحقق القيم
            if ($data['amount'] <= 0) {
                $this->validation_message = __('Amount must be greater than zero.');
                return false;
            }
            if ($data['amount'] > $remaining) {
                $this->validation_message = __('Amount exceeds remaining balance.');
                return false;
            }

            // تحديث الدفعة
            $service = $this->setService('PaymentService');
            $updated = $service->update($data, $this->payment->id, $model);

            if (!$updated) {
                $this->alertMessage(__('Failed to update payment.'), 'error');
                return false;
            }

            $this->alertMessage(__('Payment updated successfully.'), 'success');
            return redirect()->route('admin.panel.payments.list');
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while updating the payment: ') . $e->getMessage(), 'error');
            return false;
        }
    }


    public function updatedPaymentableId()
    {
        $this->loadSelectedRecord();
    }

    public function updatedPaymentableType()
    {
        $this->paymentable_id = null;
        $this->selectedRecord = null;
    }

    protected function loadSelectedRecord()
    {
        if (!$this->paymentable_id || !$this->paymentable_type) {
            $this->selectedRecord = null;
            return;
        }

        if ($this->paymentable_type === 'sale') {
            $sale = Sale::with('payments')->find($this->paymentable_id);
            if ($sale) {
                $sale->paid_amount = $sale->payments
                    ->where('id', '!=', $this->payment->id)
                    ->sum('amount');
                $this->selectedRecord = $sale;
            }
        }

        if ($this->paymentable_type === 'reservation') {
            $res = Reservation::with('payments')->find($this->paymentable_id);
            if ($res) {
                $res->paid_amount = $res->deposit; // العربون دائمًا محسوب
                $res->paid_amount += $res->payments
                    ->where('id', '!=', $this->payment->id)
                    ->sum('amount');
                $this->selectedRecord = $res;
            }
        }
    }

    public function updatedAmount($value)
    {
        if (!$this->selectedRecord) return;

        $this->validation_message = '';

        $newAmount = floatval($value);

        // منع القيم السالبة
        if ($newAmount < 0) {
            $this->amount = 0;
            $this->validation_message = __('Amount cannot be negative.');
            return;
        }

        // مجموع المدفوع بدون الدفعة الحالية
        $totalPaid = $this->selectedRecord->payments
            ->where('id', '!=', $this->payment->id)
            ->sum('amount');

        if ($this->paymentable_type === 'reservation') {
            $totalPaid += $this->selectedRecord->deposit;
        }

        // المبلغ الإجمالي المستحق
        $grandTotal = $this->selectedRecord->grand_total ?? $this->selectedRecord->total;
        $remaining = $grandTotal - $totalPaid;

        // التحقق من تجاوز المبلغ
        if ($newAmount > $remaining) {
            $this->amount = $remaining; // يضبطه على الباقي تلقائي
            $this->validation_message = __('Amount exceeds remaining balance.');
            return;
        }

        // تحديث paid_amount
        $this->selectedRecord->paid_amount = $totalPaid + $newAmount;
    }
}
