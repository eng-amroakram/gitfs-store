<?php

namespace App\Livewire\Panel\Payments;

use App\Helpers\LivewireHelper;
use App\Http\Requests\PaymentRequest;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreatePayment extends Component
{
    use LivewireHelper;

    // بيانات الدفع
    public $payment_reference; // رقم الحجز أو الفاتورة
    public $paymentable_type; // 'sale' أو 'reservation'
    public $paymentable_id;
    public $amount = 0;
    public $method = 'cash';
    public $notes;

    public $allSales = [];
    public $allReservations = [];

    // للسجّل المحدد
    public $selectedRecord = null;

    public $validation_message = '';

    public function mount()
    {
        $this->payment_reference = $this->setService('PaymentService')->generatePaymentReference();
        $this->allSales = $this->setService('SaleService')->all(
            ['status' => ['unpaid', 'partial']],
            ['id', 'invoice_number', 'customer_id', 'total', 'discount', 'grand_total', 'status']
        );

        $this->allReservations = $this->setService('ReservationService')->all(
            ['status' => ['active']],
            ['id', 'uuid', 'customer_id', 'deposit', 'remaining', 'total', 'status']
        );
    }

    #[Layout('layouts.admin.panel'), Title('Create Payment')]
    public function render()
    {
        return view('livewire.panel.payments.create-payment', [
            'allSales' => $this->allSales,
            'allReservations' => $this->allReservations,
            'selectedRecord' => $this->selectedRecord,
        ]);
    }

    public function create()
    {
        try {
            $request = new PaymentRequest();
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

            // المبالغ المدفوعة سابقاً
            $totalPaid = $model->payments->sum('amount');
            if ($data['paymentable_type'] === 'reservation') {
                $totalPaid += $model->deposit;
            }

            $grandTotal = $model->grand_total ?? $model->total;
            $remaining = $grandTotal - $totalPaid;

            // تحقق من القيمة
            if ($data['amount'] <= 0) {
                $this->validation_message = __('Amount must be greater than zero.');
                return false;
            }
            if ($data['amount'] > $remaining) {
                $this->validation_message = __('Amount exceeds remaining balance.');
                return false;
            }

            // إنشاء الدفعة
            $service = $this->setService('PaymentService');
            $payment = $service->store($data, $model);

            if (!$payment) {
                $this->alertMessage(__('Failed to create payment.'), 'error');
                return false;
            }

            $this->alertMessage(__('Payment added successfully.'), 'success');
            return redirect()->route('admin.panel.payments.list');
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the payment: ') . $e->getMessage(), 'error');
            return false;
        }
    }

    public function updatedPaymentableId()
    {
        $this->loadSelectedRecord();
    }

    public function updatedPaymentableType()
    {
        // لما يتغير النوع لازم نفرّغ الـ id والـ details
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
                $sale->paid_amount = $sale->payments->sum('amount');
                $this->selectedRecord = $sale;
            }
        }

        if ($this->paymentable_type === 'reservation') {
            $res = Reservation::with('payments')->find($this->paymentable_id);
            if ($res) {
                $res->paid_amount =  $res->deposit + $res->payments->sum('amount');
                $this->selectedRecord = $res;
            }
        }
    }

    public function updatedAmount($value)
    {
        if (!$this->selectedRecord) return;
        $this->validation_message = '';

        // القيمة الجديدة
        $newAmount = floatval($value);

        // منع القيم السالبة
        if ($newAmount < 0) {
            $this->amount = 0;
            $this->validation_message = __('Amount cannot be negative.');
            return;
        }

        // إجمالي المدفوع سابقاً
        $totalPaid = $this->selectedRecord->payments->sum('amount');

        // لو الحجز فيه عربون
        if ($this->paymentable_type === 'reservation') {
            $totalPaid += $this->selectedRecord->deposit;
        }

        // المبلغ الإجمالي المستحق
        $grandTotal = $this->selectedRecord->grand_total ?? $this->selectedRecord->total;

        // المتبقي
        $remaining = $grandTotal - $totalPaid;

        // لو المستخدم كتب مبلغ أكبر من المتبقي
        if ($newAmount > $remaining) {
            $this->amount = $remaining; // ضبطه تلقائيًا على المتبقي
            $this->validation_message = __('Amount exceeds remaining balance.');
            return;
        }

        // تحديث المجموع المدفوع بعد القيمة الجديدة
        $this->selectedRecord->paid_amount = $totalPaid + $newAmount;
    }
}
