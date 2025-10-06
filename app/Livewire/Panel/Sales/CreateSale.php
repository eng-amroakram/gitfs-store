<?php

namespace App\Livewire\Panel\Sales;

use App\Helpers\LivewireHelper;
use App\Http\Requests\SaleRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateSale extends Component
{
    use LivewireHelper;

    // بيانات الفاتورة
    public $invoice_number;
    public $customer_id;
    public $status = 'unpaid'; // الحالة الافتراضية
    public $items = []; // قائمة البنود
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
        $this->invoice_number = $this->setService('SaleService')->generateInvoiceNumber();
        $this->allItems = $this->setService('ItemService')->all(['type' => 'sale'], ['id', 'name', 'sale_price', 'available_quantity', 'code']);
        $this->allCustomers = $this->setService('CustomerService')->all(['id', 'name']);

        $this->description = "New SALE For Customer - " . $this->invoice_number;
        $this->notes = "Thank you for your business!";

        // يمكن إضافة صف افتراضي للبند
        $this->addItem();
    }

    #[Layout('layouts.admin.panel'), Title('Create Sale')]
    public function render()
    {
        return view('livewire.panel.sales.create-sale', [
            'allItems'     => $this->allItems,
            'allCustomers' => $this->allCustomers,
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

    // حفظ الفاتورة
    public function create()
    {
        try {
            $request = new SaleRequest();
            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
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
            $sale = $service->store($data);

            if ($sale) {
                $this->alertMessage(__('Sale created successfully.'), 'success');
                return redirect()->route('admin.panel.sales.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the sale: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
