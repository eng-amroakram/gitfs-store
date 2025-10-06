<?php

namespace App\Livewire\Panel\ItemMovements;

use App\Helpers\LivewireHelper;
use App\Http\Requests\ItemMovementRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditItemMovement extends Component
{
    use LivewireHelper;

    public $item_movement_id;
    public $item_id;
    public $quantity;
    public $movement_type;
    public $reason;
    public $items = [];

    public function mount()
    {
        $this->item_movement_id = session('item_movement_id');

        $itemMovementService = $this->setService('ItemMovementService');
        $item_movement = $itemMovementService->model($this->item_movement_id);

        $this->item_id = $item_movement->item_id;
        $this->quantity = $item_movement->quantity;
        $this->movement_type = $item_movement->movement_type;
        $this->reason = $item_movement->reason;

        $itemService = $this->setService('ItemService');
        $this->items = $itemService->all(['id', 'name', 'code']);
    }

    #[Layout('layouts.admin.panel'), Title('Edit Item Movement')]
    public function render()
    {
        return view('livewire.panel.item-movements.edit-item-movement');
    }

    public function update()
    {
        try {
            $request = new ItemMovementRequest();
            $request->merge(['item_movement_id' => $this->item_movement_id]);

            $rules = $request->rules();
            $messages = $request->messages();

            $data = Validator::make([
                'item_movement_id' => $this->item_movement_id,
                'item_id'          => $this->item_id,
                'quantity'         => $this->quantity,
                'movement_type'    => $this->movement_type,
                'reason'           => $this->reason,
            ], $rules, $messages)->validate();

            $service = $this->setService('ItemMovementService');
            $item_movement = $service->update($data, $this->item_movement_id);

            if ($item_movement) {
                $this->alertMessage(__('Item movement updated successfully.'), 'success');
                return redirect()->route('admin.panel.item-movements.list');
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
