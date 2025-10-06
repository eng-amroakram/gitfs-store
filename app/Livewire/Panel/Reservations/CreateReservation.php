<?php

namespace App\Livewire\Panel\Reservations;

use App\Helpers\LivewireHelper;
use App\Http\Requests\ReservationRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateReservation extends Component
{
    use LivewireHelper;

    public $reservation_number;
    public $customer_id;
    public $start_date;
    public $end_date;
    public $deposit = 0;
    public $status = 'active';

    public $items = [];
    public $allItems = [];
    public $allCustomers = [];

    public $total = 0;
    public $remaining = 0;
    public $description;
    public $notes;

    protected $listeners = ['selectUpdated'];

    public function mount()
    {
        $this->reservation_number = $this->setService('ReservationService')->generateReservationNumber();
        $this->allCustomers = $this->setService('CustomerService')->all(['id', 'name']);
        $this->allItems = $this->setService('ItemService')->all([
            'type' => 'rental',
        ], [
            'id',
            'name',
            'sale_price',
            'code',
            'quantity_total',
            'available_quantity',
            'reserved_quantity'
        ]);
        $this->addItem();
        $this->description = "New RESERVATION For Customer";
        $this->notes = "Thank you for your business!";
    }

    #[Layout('layouts.admin.panel'), Title('Create Reservation')]
    public function render()
    {
        return view('livewire.panel.reservations.create-reservation', [
            'allCustomers' => $this->allCustomers,
            'allItems'     => $this->allItems,
        ]);
    }

    public function addItem()
    {
        $this->items[] = [
            'item_id' => '',
            'quantity' => 1,
            'price' => 0,
            'subtotal' => 0
        ];
        $this->recalculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateTotals();
    }

    public function updatedItems($value, $name)
    {
        if (str_ends_with($name, 'item_id')) {
            $index = explode('.', $name)[0];
            $item = Item::find($value);
            if ($item) {
                $this->items[$index]['price'] = $item->sale_price;
                $this->items[$index]['quantity'] = 1;
                $this->items[$index]['subtotal'] = $item->sale_price;
            }
        }

        $this->recalculateTotals();
    }

    public function selectUpdated($index, $value)
    {
        $this->items[$index]['item_id'] = $value;
        $item = Item::find($value);
        if ($item) {
            $this->items[$index]['price'] = $item->sale_price;
            $this->items[$index]['quantity'] = 1;
            $this->items[$index]['subtotal'] = $item->sale_price;
        }
        $this->recalculateTotals();
    }

    public function recalculateTotals()
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
        $this->remaining = max($this->total - ($this->deposit ?? 0), 0);
    }

    public function create()
    {
        try {
            $request = new ReservationRequest();
            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'reservation_number' => $this->reservation_number,
                'customer_id' => $this->customer_id,
                // 'user_id'     => Auth::id(),
                'start_date'  => $this->start_date,
                'end_date'    => $this->end_date,
                'deposit'     => $this->deposit,
                'status'      => $this->status,
                'items'       => $this->items,
                'total'       => collect($this->items)->sum('subtotal'),
                'remaining'   => collect($this->items)->sum('subtotal') - $this->deposit,
                'description' => $this->description,
                'notes'       => $this->notes,
            ], $rules, $messages)->validate();

            $service = $this->setService('ReservationService');
            $reservation = $service->store($data);

            if ($reservation) {
                $this->alertMessage(__('Reservation created successfully.'), 'success');
                return redirect()->route('admin.panel.reservations.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the reservation: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
