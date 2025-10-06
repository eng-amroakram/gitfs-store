<?php

namespace App\Livewire\Panel\Purchases;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowPurchase extends Component
{
    use LivewireHelper;

    public $purchase_id;
    public $purchase;

    public function mount()
    {
        $id = session('purchase_id');

        if (!$id) {
            return redirect()->route('admin.panel.purchases.list');
        }

        $this->purchase_id = $id;
        $service =  $this->setService('PurchaseService');
        $this->purchase = $service->model($this->purchase_id);
    }

    #[Layout('layouts.admin.panel'), Title('Show Purchase')]
    public function render()
    {
        return view('livewire.panel.purchases.show-purchase', ['purchase' => $this->purchase]);
    }
}
