<?php

namespace App\Livewire\Panel\Customers;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;

class CustomersList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'asc';

    public $selectedCustomers = [];
    public $selectAll = false;

    public $search = "";
    public $status = "";
    public $filters = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            // حدد كل العناصر في الصفحة الحالية فقط
            $this->selectedCustomers = $this->getCurrentPageCustomersIds();
        } else {
            $this->selectedCustomers = [];
        }
    }

    public function loadCustomers()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
        ];

        $service = $this->setService('CustomerService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageCustomersIds()
    {
        $customers = $this->loadCustomers();
        return $customers->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('العملاء')]
    public function render()
    {
        $customers = $this->loadCustomers();
        return view('livewire.panel.customers.customers-list', compact('customers'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.customers.create');
    }

    public function edit($id)
    {
        session(['customer_id' => $id]);
        return redirect()->route('admin.panel.customers.edit');
    }


    public function show($id)
    {
        session(['customer_id' => $id]);
        return redirect()->route('admin.panel.customers.show');
    }
}
