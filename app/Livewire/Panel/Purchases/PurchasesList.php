<?php

namespace App\Livewire\Panel\Purchases;

use App\Helpers\LivewireHelper;
use App\Models\Purchase;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class PurchasesList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'desc';

    public $selectedPurchases = [];
    public $selectAll = false;

    public $search = '';
    public $supplier_id = '';
    public $filters = [];

    public $deleteId = null;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPurchases = $this->getCurrentPagePurchaseIds();
        } else {
            $this->selectedPurchases = [];
        }
    }

    public function loadPurchases()
    {
        $this->filters = [
            'search' => $this->search,
            'supplier_id' => $this->supplier_id,
        ];

        $service = $this->setService('PurchaseService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPagePurchaseIds()
    {
        $purchases = $this->loadPurchases();
        return $purchases->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)
            ->pluck('id')
            ->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('Purchases List')]
    public function render()
    {
        $purchases = $this->loadPurchases();
        return view('livewire.panel.purchases.purchases-list', compact('purchases'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.purchases.create');
    }

    public function edit($id)
    {
        session(['purchase_id' => $id]);
        return redirect()->route('admin.panel.purchases.edit');
    }

    public function show($id)
    {
        session(['purchase_id' => $id]);
        return redirect()->route('admin.panel.purchases.show');
    }


    public function confirmDelete(Purchase $purchase)
    {
        $this->deleteId = $purchase->id;
        $this->alertConfirm('هل أنت متأكد أنك تريد حذف طلب الشراء', 'delete');
    }

    public function delete()
    {
        try {
            $service = $this->setService('PurchaseService');
            $purchase = $service->delete($this->deleteId);

            if ($purchase) {
                $this->alertMessage(__('Purchase deleted successfully.'), 'success');
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
