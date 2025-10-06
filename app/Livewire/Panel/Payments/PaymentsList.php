<?php

namespace App\Livewire\Panel\Payments;

use App\Helpers\LivewireHelper;
use App\Models\Payment;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentsList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'desc';

    public $selectedPayments = [];
    public $selectAll = false;

    public $search = '';
    public $filters = [];

    public $deleteId = null;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPayments = $this->getCurrentPagePaymentsIds();
        } else {
            $this->selectedPayments = [];
        }
    }

    public function loadPayments()
    {
        $this->filters = [
            'search' => $this->search,
        ];

        $service =  $this->setService('PaymentService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPagePaymentsIds()
    {
        $payments = $this->loadPayments();
        return $payments->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('Payments List')]
    public function render()
    {
        $payments = $this->loadPayments();
        return view('livewire.panel.payments.payments-list', compact('payments'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.payments.create');
    }

    public function edit($id)
    {
        session(['payment_id' => $id]);
        return redirect()->route('admin.panel.payments.edit');
    }

    public function show($id)
    {
        session(['payment_id' => $id]);
        return redirect()->route('admin.panel.payments.show');
    }

    public function confirmDelete(Payment $payment)
    {
        $this->deleteId = $payment->id;
        $this->alertConfirm('هل أنت متأكد أنك تريد حذف الدفعة؟', 'delete');
    }

    public function delete()
    {
        try {
            $service = $this->setService('PaymentService');
            $payment = $service->delete($this->deleteId);

            if ($payment) {
                $this->alertMessage(__('Payment deleted successfully.'), 'success');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the payment: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}
