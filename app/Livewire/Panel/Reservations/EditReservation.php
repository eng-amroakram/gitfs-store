<?php

namespace App\Livewire\Panel\Reservations;

use App\Http\Requests\ReservationRequest;
use App\Helpers\LivewireHelper;
use App\Models\Item;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditReservation extends Component
{
    use LivewireHelper;

    public $reservation_id;
    public $reservation_number;
    public $customer_id;
    public $start_date;
    public $end_date;
    public $deposit = 0;
    public $status;
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
        $this->reservation_id = session('reservation_id');

        if (!$this->reservation_id) {
            $this->alertMessage(__('Reservation ID is missing.'), 'error');
            return redirect()->route('admin.panel.reservations.list');
        }

        $reservation = Reservation::with('items')->findOrFail($this->reservation_id);

        if (!$reservation) {
            $this->alertMessage(__('Reservation not found.'), 'error');
            return redirect()->route('admin.panel.reservations.list');
        }

        $this->reservation_number = $reservation->reservation_number;
        $this->customer_id = $reservation->customer_id;
        $this->start_date = $reservation->start_date;
        $this->end_date = $reservation->end_date;
        $this->status = $reservation->status;
        $this->deposit = $reservation->deposit;
        $this->description = $reservation->description;
        $this->notes = $reservation->notes;

        $this->allItems = $this->setService('ItemService')->all(['type' => 'rental'], [
            'id',
            'name',
            'sale_price',
            'code',
            'quantity_total',
            'available_quantity',
            'reserved_quantity'
        ]);

        $this->allCustomers = $this->setService('CustomerService')->all(['id', 'name']);

        // تحميل البنود الحالية
        foreach ($reservation->items as $item) {
            $this->items[] = [
                'id' => +$item->id,
                'item_id' => $item->item_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal
            ];
        }

        // في حالة عدم وجود بنود
        if (empty($this->items)) {
            $this->addItem();
        }

        $this->recalculateTotals();
    }

    #[Layout('layouts.admin.panel'), Title('Edit Reservation')]
    public function render()
    {
        return view('livewire.panel.reservations.edit-reservation');
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
            } else {
                $this->items[$index]['price'] = 0;
                $this->items[$index]['quantity'] = 1;
                $this->items[$index]['subtotal'] = 0;
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

    public function update()
    {
        try {
            $request = new ReservationRequest();
            $request->merge([
                'reservation_id' => $this->reservation_id
            ]);
            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'reservation_number' => $this->reservation_number,
                'customer_id' => $this->customer_id,
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
            $reservation = $service->update($data, $this->reservation_id);

            if ($reservation) {
                $this->alertMessage(__('Reservation updated successfully.'), 'success');
                return redirect()->route('admin.panel.reservations.list');
            } else {
                $this->alertMessage(__('Failed to update reservation.'), 'error');
            }
        } catch (ValidationException $e) {
            $this->alertMessage($e->getMessage(), 'error');
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while updating the reservation: ') . $e->getMessage(), 'error');
        }
        return false;
    }
}
