<?php

namespace App\Livewire\Panel\Suppliers;

use App\Helpers\LivewireHelper;
use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowSupplier extends Component
{
    use LivewireHelper;

    public $supplier_id;
    public $supplier;

    public function mount()
    {
        $supplier_id = session('supplier_id');
        if (!$supplier_id) {
            return redirect()->route('admin.panel.suppliers.list');
        }

        $this->supplier_id = $supplier_id;

        $this->supplier = Supplier::with([
            'purchases' => function ($q) {
                $q->latest();
            },
            'purchases.user',
        ])->findOrFail($this->supplier_id);
    }

    #[Layout('layouts.admin.panel'), Title('Show Supplier')]
    public function render()
    {
        return view('livewire.panel.suppliers.show-supplier');
    }
}
