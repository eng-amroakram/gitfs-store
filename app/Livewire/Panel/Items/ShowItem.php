<?php

namespace App\Livewire\Panel\Items;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowItem extends Component
{
    use LivewireHelper;

    public $item_id;
    public $item;

    public function mount()
    {
        $id = session('item_id');

        if (!$id) {
            return redirect()->route('admin.panel.items.list');
        }

        $this->item_id = $id;
        $service = $this->setService('ItemService');
        $this->item = $service->model($this->item_id);
    }
    #[Layout('layouts.admin.panel'), Title('Show Item')]
    public function render()
    {
        return view('livewire.panel.items.show-item', ['item' => $this->item]);
    }
}
