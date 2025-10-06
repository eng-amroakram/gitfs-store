<?php

namespace App\Livewire\Panel\ItemMovements;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowItemMovement extends Component
{
    use LivewireHelper;

    public $item_movement_id;
    public $item_movement;

    public function mount()
    {
        $id = session('item_movement_id');

        if (!$id) {
            return redirect()->route('admin.panel.item-movements.list');
        }

        $this->item_movement_id = $id;
        $service =  $this->setService('ItemMovementService');
        $this->item_movement = $service->model($this->item_movement_id);
    }

    #[Layout('layouts.admin.panel'), Title('Show Item Movement')]
    public function render()
    {
        return view('livewire.panel.item-movements.show-item-movement', ['movement' => $this->item_movement]);
    }
}
