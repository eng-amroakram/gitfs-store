<?php

namespace App\Livewire\Panel\Sales;

use App\Helpers\LivewireHelper;
use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditSale extends Component
{
    use LivewireHelper;

    public $sale_id;
    public $invoice_number;
    public $customer_id;
    public $status;
    public $items = [];
    public $allItems = [];
    public $allCustomers = [];

    public $total = 0;
    public $discount = 0;
    public $grand_total = 0;
    public $description;
    public $notes;

    protected $listeners = ['selectUpdated'];

    public function mount()
    {
        $this->sale_id = session('sale_id');
        if (!$this->sale_id) {
            $this->alertMessage(__('Sale ID is missing.'), 'error');
            return redirect()->route('admin.panel.sales.list');
        }
        $sale = Sale::with('items')->findOrFail($this->sale_id);

        $this->invoice_number = $sale->invoice_number;
        $this->customer_id = $sale->customer_id;
        $this->status = $sale->status;
        $this->discount = $sale->discount;
        $this->description = $sale->description;
        $this->notes = $sale->notes;

        $this->allItems = $this->setService('ItemService')->all([
            'type' => 'sale'
        ], [
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
        foreach ($sale->items as $item) {
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

    #[Layout('layouts.admin.panel'), Title('Edit Sale')]
    public function render()
    {
        return view('livewire.panel.sales.edit-sale');
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
        $this->grand_total = max($this->total - ($this->discount ?? 0), 0);
    }

    public function update()
    {
        try {
            $request = new SaleRequest();
            $request->merge(['sale_id' => $this->sale_id]);

            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'sale_id'        => $this->sale_id,
                'invoice_number' => $this->invoice_number,
                'customer_id'    => $this->customer_id,
                'user_id'        => Auth::id(),
                'discount'       => $this->discount,
                'status'         => $this->status,
                'items'          => $this->items,
                'total'          => collect($this->items)->sum('subtotal'),
                'grand_total'    => collect($this->items)->sum('subtotal') - $this->discount,
                'description'    => $this->description,
                'notes'          => $this->notes,
            ], $rules, $messages)->validate();

            $service = $this->setService('SaleService');
            $sale = $service->update($data, $this->sale_id);

            if ($sale) {
                $this->alertMessage(__('Sale updated successfully.'), 'success');
                return redirect()->route('admin.panel.sales.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while updating the sale: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
