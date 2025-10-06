<?php

namespace App\Livewire\Employees;

use App\Helpers\LivewireHelper;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\SaleRequest;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


class Cashier extends Component
{
    use LivewireHelper;

    public $invoice_number = '';
    public $reservation_number = '';
    public $payment_reference = '';
    public $type = 'sale'; // sale | reservation
    public $items = [];
    public $deposit = 0; // للحجز فقط
    public $amount_paid = 0;
    public $total = 0;
    public $discount = 0;
    public $grand_total = 0;
    public $customer;
    public $reservation_status = 'active'; // للحجز فقط
    public $start_date;
    public $end_date;
    public $description = '';
    public $notes = '';

    protected $listeners = ['selectUpdated'];

    public function mount()
    {
        $this->invoice_number = Sale::generateInvoiceNumber();
        $this->reservation_number = Reservation::generateReservationNumber();
        $this->payment_reference = Payment::generatePaymentReference();
        $this->customer = Customer::firstOrCreate(['name' => 'زبون']);
        $this->addItem();
    }

    public function selectUpdated($index, $value)
    {
        $this->items[$index]['item_id'] = $value;
        $item = Item::find($value);
        if ($item) {
            $this->items[$index]['price'] = $item->sale_price;
            $this->calculateTotals();
        }
    }

    #[Layout('layouts.admin.panel'), Title('Cashier')]
    public function render()
    {
        $allRentalItems = $this->setService('ItemService')->all(['type' => 'rental',], [
            'id',
            'name',
            'sale_price',
            'code',
            'quantity_total',
            'available_quantity',
            'reserved_quantity'
        ]);

        $allSaleItems = $this->setService('ItemService')->all(['type' => 'sale',], [
            'id',
            'name',
            'sale_price',
            'code',
            'quantity_total',
            'available_quantity',
            'reserved_quantity'
        ]);

        return view('livewire.employees.cashier', compact('allRentalItems', 'allSaleItems'));
    }

    public function addItem()
    {
        $this->items[] = [
            'item_id' => '',
            'quantity' => 1,
            'price' => 0,
            'subtotal' => 0
        ];
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $name)
    {
        if (str_ends_with($name, 'item_id')) {
            $index = explode('.', $name)[0];
            $item = Item::find($value);
            if ($item) {
                $this->items[$index]['price'] = $item->sale_price;
                $this->items[$index]['quantity'] = 1; // Reset quantity to 1 when item is selected
                $this->items[$index]['subtotal'] = $item->sale_price;
            } else {
                $this->items[$index]['price'] = 0;
                $this->items[$index]['quantity'] = 0;
                $this->items[$index]['subtotal'] = 0;
            }
        }
        $this->calculateTotals();
    }

    public function updatedDiscount()
    {
        $this->calculateTotals();
    }

    public function updatedDeposit()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $total = 0;
        foreach ($this->items as $i => $row) {
            $quantity = $row['quantity'] ?? 0;
            $price = $row['price'] ?? 0;
            $subtotal = $quantity * $price;
            $this->items[$i]['subtotal'] = $subtotal;
            $total += $subtotal;
        }
        $this->total = $total;
        $this->grand_total = $this->total - $this->discount;

        if ($this->type === 'reservation' && $this->deposit) {
            $this->grand_total = max($this->grand_total - $this->deposit, 0);
        }
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            $customerId = $this->customer->id;

            if ($this->type === 'sale') {
                $request = new SaleRequest();
                $rules = $request->rules();
                $messages = $request->messages();

                $data = Validator::make([
                    'invoice_number' => $this->invoice_number,
                    'customer_id'    => $customerId,
                    'user_id'        => Auth::id(),
                    'discount'       => $this->discount,
                    'status'         => $this->amount_paid >= $this->grand_total ? 'paid' : ($this->amount_paid > 0 ? 'partial' : 'unpaid'),
                    'items'          => $this->items,
                    'total'          => $this->total,
                    'grand_total'    => $this->grand_total,
                    'description'    => "",
                    'notes'          => "",
                ], $rules, $messages)->validate();

                $data['paid'] = $this->amount_paid;
                $data['remaining'] = max($this->grand_total - $this->amount_paid, 0);
                $data['status'] = $this->amount_paid >= $this->grand_total ? 'paid' : ($this->amount_paid > 0 ? 'partial' : 'unpaid');

                $service = $this->setService("SaleService");
                $sale = $service->store($data);

                $paymentType = "sale";
                $model = $sale;
            } else {

                $request = new ReservationRequest();
                $rules = $request->rules();
                $messages = $request->messages();

                $data = Validator::make([
                    'reservation_number' => $this->reservation_number,
                    'customer_id' => $customerId,
                    'start_date'  => $this->start_date,
                    'end_date'    => $this->end_date,
                    'status'      => "active",
                    'deposit'     => $this->deposit,
                    'total'       => $this->grand_total,
                    'remaining'   => max($this->grand_total - $this->amount_paid, 0),
                    'items'       => $this->items,
                    'description' => $this->description,
                    'notes'       => $this->notes,
                ], $rules, $messages)->validate();

                $data['discount'] = $this->discount;

                $service = $this->setService('ReservationService');
                $reservation = $service->store($data);

                $paymentType = "reservation";
                $model = $reservation;
            }

            if ($this->amount_paid > 0 && $model) {

                $request = new PaymentRequest();
                $rules = $request->rules();
                $messages = $request->messages();

                $data = Validator::make([
                    'payment_reference' => $this->payment_reference,
                    'paymentable_type' => $paymentType,
                    'paymentable_id'   => $model->id,
                    'amount'           => $this->amount_paid,
                    'method'           => 'cash',
                    'notes'            => "",
                ], $rules, $messages)->validate();

                // المبالغ المدفوعة سابقاً
                $totalPaid = $model->payments->sum('amount');
                if ($data['paymentable_type'] === 'reservation') {
                    $totalPaid += $model->deposit;
                }

                $grandTotal = $model->grand_total ?? $model->total;
                $remaining = $grandTotal - $totalPaid;

                // تحقق من القيمة
                if ($data['amount'] <= 0) {
                    $this->alertMessage(__('Amount must be greater than zero.'), 'error');
                    return false;
                }

                if ($data['amount'] > $remaining) {
                    $this->alertMessage(__('Amount exceeds remaining balance.'), 'error');
                    return false;
                }

                $service = $this->setService('PaymentService');
                $payment = $service->store($data, $model);

                if (!$payment) {
                    $this->alertMessage(__('Failed to create payment.'), 'error');
                    return false;
                }
            }

            DB::commit();
            $this->reset(['items', 'amount_paid', 'total', 'discount', 'grand_total']);
            $this->addItem();
            $this->alertMessage(__('Transaction saved successfully ✅'), 'success');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            $this->alertMessage('Error: ' . $e->getMessage(), 'error');
        }
    }
}
