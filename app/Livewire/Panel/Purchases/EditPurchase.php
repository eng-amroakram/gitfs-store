<?php

namespace App\Livewire\Panel\Purchases;

use App\Helpers\LivewireHelper;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditPurchase extends Component
{
    use LivewireHelper;

    public $purchase_id;
    public $invoice_number;
    public $supplier_id;
    public $items = [];
    public $allItems = [];
    public $allSuppliers = [];

    public $total = 0;

    protected $listeners = ['selectUpdated'];

    public function mount()
    {
        $this->purchase_id = session('purchase_id');
        if (!$this->purchase_id) {
            $this->alertMessage(__('Purchase ID is missing.'), 'error');
            return redirect()->route('admin.panel.purchases.list');
        }

        $purchase = Purchase::with('items')->findOrFail($this->purchase_id);

        $this->invoice_number = $purchase->invoice_number;
        $this->supplier_id    = $purchase->supplier_id;

        $this->allItems     = $this->setService('ItemService')->all(['id', 'name', 'purchase_price', 'quantity', 'code']);
        $this->allSuppliers = $this->setService('SupplierService')->all(['id', 'name']);

        // تحميل البنود
        foreach ($purchase->items as $item) {
            $this->items[] = [
                'item_id'  => $item->id,
                'quantity' => $item->quantity,
                'price'    => $item->price,
                'subtotal' => $item->subtotal
            ];
        }

        if (empty($this->items)) {
            $this->addItem();
        }

        $this->recalculateTotals();
    }

    #[Layout('layouts.admin.panel'), Title('Edit Purchase')]
    public function render()
    {
        return view('livewire.panel.purchases.edit-purchase');
    }

    public function addItem()
    {
        $this->items[] = [
            'item_id'  => '',
            'quantity' => 1,
            'price'    => 0,
            'subtotal' => 0,
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
            $item  = Item::find($value);
            if ($item) {
                $this->items[$index]['price']    = $item->purchase_price;
                $this->items[$index]['quantity'] = 1;
                $this->items[$index]['subtotal'] = $item->purchase_price;
            } else {
                $this->items[$index]['price']    = 0;
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
            $this->items[$index]['price']    = $item->purchase_price;
            $this->items[$index]['quantity'] = 1;
            $this->items[$index]['subtotal'] = $item->purchase_price;
        }
        $this->recalculateTotals();
    }

    public function recalculateTotals()
    {
        $total = 0;
        foreach ($this->items as $i => $row) {
            $quantity              = $row['quantity'] ?? 0;
            $price                 = $row['price'] ?? 0;
            $subtotal              = $quantity * $price;
            $this->items[$i]['subtotal'] = $subtotal;
            $total += $subtotal;
        }
        $this->total = $total;
    }

    public function update()
    {
        try {
            $request  = new PurchaseRequest();

            $request->merge(['purchase_id' => $this->purchase_id]);

            $rules    = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'purchase_id'   => $this->purchase_id,
                'invoice_number' => $this->invoice_number,
                'supplier_id'    => $this->supplier_id,
                'user_id'        => Auth::id(),
                'items'          => $this->items,
                'total'          => collect($this->items)->sum('subtotal'),
            ], $rules, $messages)->validate();

            $service  = $this->setService('PurchaseService');
            $purchase = $service->update($data, $this->purchase_id);

            if ($purchase) {
                $this->alertMessage(__('Purchase updated successfully.'), 'success');
                return redirect()->route('admin.panel.purchases.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while updating the purchase: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
