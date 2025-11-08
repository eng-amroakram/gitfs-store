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

class CreateItem extends Component
{
    use LivewireHelper;

    public $code, $name, $description, $purchase_price, $sale_price, $quantity, $type = 'sale', $low_stock_alert = 1;

    public function mount()
    {
        $service = $this->setService('ItemService');
        $this->code = $service->generateItemCode();
    }

    #[Layout('layouts.admin.panel'), Title('Create Item')]
    public function render()
    {
        return view('livewire.panel.items.create-item');
    }

    public function create()
    {
        try {
            $request = new ItemRequest();
            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'code'            => $this->code,
                'name'            => $this->name,
                'purchase_price'  => $this->purchase_price,
                'sale_price'      => $this->sale_price,
                'quantity'        => $this->quantity,
                'type'            => $this->type,
                'low_stock_alert' => $this->low_stock_alert,
                'description'     => $this->description,
            ], $rules, $messages)->validate();


            // ✅ التحقق من الأسعار
            if ($data['purchase_price'] < 0) {
                throw ValidationException::withMessages([
                    'purchase_price' => __('Quantity must be greater than zero.'),
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

            // ✅ تحقق من الكود
            if (Item::where('code', $data['code'])->exists()) {
                throw ValidationException::withMessages([
                    'code' => __('Item code is already in use, please enter a different code.'),
                ]);
            }

            // Set initial quantity to quantity_total
            $data['quantity_total'] = $data['quantity'];
            $data['reserved_quantity'] = 0;
            $data['available_quantity'] = $data['quantity'];

            $service = $this->setService('ItemService');
            $item = $service->store($data);

            if ($item) {
                $this->alertMessage(__('Item created successfully.'), 'success');
                return redirect()->route('admin.panel.items.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the item: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
