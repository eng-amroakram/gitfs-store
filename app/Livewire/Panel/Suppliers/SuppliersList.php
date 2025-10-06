<?php

namespace App\Livewire\Panel\Suppliers;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;

class SuppliersList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'asc';

    public $selectedSuppliers = [];
    public $selectAll = false;

    public $search = "";
    public $status = "";
    public $filters = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            // حدد كل العناصر في الصفحة الحالية فقط
            $this->selectedSuppliers = $this->getCurrentPageSuppliersIds();
        } else {
            $this->selectedSuppliers = [];
        }
    }

    public function loadSuppliers()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
        ];

        $service = $this->setService('SupplierService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageSuppliersIds()
    {
        $suppliers = $this->loadSuppliers();
        return $suppliers->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('الموردين')]
    public function render()
    {
        $suppliers = $this->loadSuppliers();
        return view('livewire.panel.suppliers.suppliers-list', compact('suppliers'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.suppliers.create');
    }

    public function edit($id)
    {
        session(['supplier_id' => $id]);
        return redirect()->route('admin.panel.suppliers.edit');
    }

    public function show($id)
    {
        session(['supplier_id' => $id]);
        return redirect()->route('admin.panel.suppliers.show');
    }
}
