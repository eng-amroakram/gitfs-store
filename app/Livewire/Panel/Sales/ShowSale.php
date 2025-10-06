<?php

namespace App\Livewire\Panel\Sales;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowSale extends Component
{
    use LivewireHelper;

    public $sale_id;
    public $sale;

    public function mount()
    {
        $id = session('sale_id');

        if (!$id) {
            return redirect()->route('admin.panel.sales.list');
        }

        $this->sale_id = $id;
        $service =  $this->setService('SaleService');
        $this->sale = $service->model($this->sale_id);
    }

    #[Layout('layouts.admin.panel'), Title('Show Sale')]
    public function render()
    {
        return view('livewire.panel.sales.show-sale', ['sale' => $this->sale]);
    }
}
