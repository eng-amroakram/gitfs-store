<?php

namespace App\Livewire\Panel\Purchases;

use App\Helpers\LivewireHelper;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreatePurchase extends Component
{
    use LivewireHelper;

    // بيانات فاتورة الشراء
    public $invoice_number;
    public $supplier_id;
    public $items = []; // قائمة البنود
    public $allItems = [];
    public $allSuppliers = [];

    public $total = 0;

    protected $listeners = ['selectUpdated'];

    public function mount()
    {
        $this->invoice_number = $this->setService('PurchaseService')->generateInvoiceNumber();
        $this->allItems = $this->setService('ItemService')->all(['id', 'name', 'purchase_price', 'quantity', 'code']);
        $this->allSuppliers = $this->setService('SupplierService')->all(['id', 'name']);

        // إضافة صف افتراضي للبند
        $this->addItem();
    }

    #[Layout('layouts.admin.panel'), Title('Create Purchase')]
    public function render()
    {
        $suppliers = $this->setService('SupplierService')->all(['id', 'name']);
        $allItems = $this->setService('ItemService')->all(['id', 'name', 'purchase_price', 'quantity', 'code']);
        return view('livewire.panel.purchases.create-purchase', compact('suppliers', 'allItems'));
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
                $this->items[$index]['price'] = $item->purchase_price;
                $this->items[$index]['quantity'] = 1;
                $this->items[$index]['subtotal'] = $item->purchase_price;
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
            $this->items[$index]['price'] = $item->purchase_price;
            $this->items[$index]['quantity'] = 1;
            $this->items[$index]['subtotal'] = $item->purchase_price;
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
    }

    // حفظ فاتورة الشراء
    public function create()
    {
        try {
            $request = new PurchaseRequest();

            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'invoice_number' => $this->invoice_number,
                'supplier_id'    => $this->supplier_id,
                'user_id'        => Auth::id(),
                'items'          => $this->items,
                'total'          => collect($this->items)->sum('subtotal'),
            ], $rules, $messages)->validate();

            $service = $this->setService('PurchaseService');
            $purchase = $service->store($data);

            if ($purchase) {
                $this->alertMessage(__('Purchase created successfully.'), 'success');
                return redirect()->route('admin.panel.purchases.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the purchase: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
