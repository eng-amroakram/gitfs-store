<?php

namespace App\Livewire\Panel\ItemMovements;

use App\Helpers\LivewireHelper;
use App\Http\Requests\ItemMovementRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateItemMovement extends Component
{
    use LivewireHelper;

    public $item_id;
    public $quantity;
    public $movement_type = 'in';
    public $reason;
    public $items = [];

    public function mount()
    {
        $service = $this->setService('ItemService');
        $this->items = $service->all(['id', 'name', 'code']);
    }

    #[Layout('layouts.admin.panel'), Title('Create Item Movement')]
    public function render()
    {
        return view('livewire.panel.item-movements.create-item-movement');
    }

    public function create()
    {
        try {
            $request = new ItemMovementRequest();
            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'item_id'       => $this->item_id,
                'quantity'      => $this->quantity,
                'movement_type' => $this->movement_type,
                'reason'        => $this->reason,
            ], $rules, $messages)->validate();

            $service = $this->setService('ItemMovementService');
            $item_movement = $service->store($data);

            if ($item_movement) {
                $this->alertMessage(__('Item movement created successfully.'), 'success');
                return redirect()->route('admin.panel.item-movements.list');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->errors());
            $this->alertMessage(__('Please correct the errors in the form.'), 'error');
            return false;
        } catch (\Exception $e) {

            $this->alertMessage(__('An error occurred while creating the item movement.'), 'error');
            return false;
        }
    }
}
