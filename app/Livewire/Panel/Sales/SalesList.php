<?php

namespace App\Livewire\Panel\Sales;

use App\Helpers\LivewireHelper;
use App\Models\Sale;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class SalesList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'desc';

    public $selectedSales = [];
    public $selectAll = false;

    public $search = '';
    public $status = '';
    public $filters = [];

    public $deleteId = null;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSales = $this->getCurrentPageSalesIds();
        } else {
            $this->selectedSales = [];
        }
    }

    public function loadSales()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
        ];

        $service =  $this->setService('SaleService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageSalesIds()
    {
        $sales = $this->loadSales();
        return $sales->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('Sales List')]
    public function render()
    {
        $sales = $this->loadSales();
        return view('livewire.panel.sales.sales-list', compact('sales'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.sales.create');
    }

    public function edit($id)
    {
        session(['sale_id' => $id]);
        return redirect()->route('admin.panel.sales.edit');
    }

    public function show($id)
    {
        session(['sale_id' => $id]);
        return redirect()->route('admin.panel.sales.show');
    }

    public function confirmDelete(Sale $sale)
    {
        $this->deleteId = $sale->id;
        $this->alertConfirm('هل أنت متأكد أنك تريد حذف المبيعة؟', 'delete');
    }

    public function delete()
    {
        try {
            $service = $this->setService('SaleService');
            $sale = $service->delete($this->deleteId);

            if ($sale) {
                $this->alertMessage(__('Sale deleted successfully.'), 'success');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the sale: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
