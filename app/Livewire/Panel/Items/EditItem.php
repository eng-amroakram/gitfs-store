<?php

namespace App\Livewire\Panel\Items;

use App\Helpers\LivewireHelper;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditItem extends Component
{
    use LivewireHelper;

    public $itemId, $code, $name, $description, $purchase_price, $sale_price, $quantity, $type, $low_stock_alert;

    public function mount()
    {
        $this->itemId = session('item_id');

        $service = $this->setService('ItemService');
        $item = $service->model($this->itemId);

        $this->code = $item->code;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->purchase_price = $item->purchase_price;
        $this->sale_price = $item->sale_price;
        $this->quantity = $item->quantity_total;
        $this->type = $item->type;
        $this->low_stock_alert = $item->low_stock_alert;
    }

    #[Layout('layouts.admin.panel'), Title('Edit Item')]
    public function render()
    {
        return view('livewire.panel.items.edit-item');
    }

    public function update()
    {
        try {
            $request = new ItemRequest();
            $request->merge(['item_id' => $this->itemId]);

            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'item_id'         => $this->itemId,
                'code'            => $this->code,
                'name'            => $this->name,
                'description'     => $this->description,
                'purchase_price'  => $this->purchase_price,
                'sale_price'      => $this->sale_price,
                'quantity'        => $this->quantity,
                'type'            => $this->type,
                'low_stock_alert' => $this->low_stock_alert,
            ], $rules, $messages)->validate();


            // ✅ التحقق من الأسعار
            if ($data['purchase_price'] < 0) {
                throw ValidationException::withMessages([
                    'purchase_price' => __('Purchase price must be greater than zero.'),
                ]);
            }

            if ($data['sale_price'] < 0) {
                throw ValidationException::withMessages([
                    'sale_price' => __('Sale price must be greater than zero.'),
                ]);
            }

            // إذا سعر البيع < سعر الشراء
            if (($data['sale_price'] < $data['purchase_price']) && $data['type'] === 'sale') {
                throw ValidationException::withMessages([
                    'sale_price' => __('Sale price cannot be less than purchase price.'),
                ]);
            }

            // ✅ التحقق من الكمية
            if ($data['quantity'] < 0) {
                throw ValidationException::withMessages([
                    'quantity' => __('Quantity must be greater than zero.'),
                ]);
            }

            // ✅ تحقق من الكود (مع استثناء نفس الصنف)
            if (Item::where('code', $data['code'])
                ->where('id', '!=', $this->itemId)
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'code' => __('Item code is already in use, please enter a different code.'),
                ]);
            }

            // Set initial quantity to quantity_total
            $data['quantity_total'] = $data['quantity'];
            $data['reserved_quantity'] = 0;
            $data['available_quantity'] = $data['quantity'];


            $service = $this->setService('ItemService');
            $item = $service->update($data, $this->itemId);

            if ($item) {
                $this->alertMessage(__('Item updated successfully.'), 'success');
                return redirect()->route('admin.panel.items.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('Unexpected error: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
