<?php

namespace App\Livewire\Panel\Payments;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowPayment extends Component
{
    use LivewireHelper;

    public $payment_id;
    public $payment;

    public function mount()
    {
        $id = session('payment_id');

        if (!$id) {
            return redirect()->route('admin.panel.payments.list');
        }

        $this->payment_id = $id;
        $service =  $this->setService('PaymentService');
        $this->payment = $service->model($this->payment_id);
    }
    #[Layout('layouts.admin.panel'), Title('Show Payment')]
    public function render()
    {
        return view('livewire.panel.payments.show-payment', ['payment' => $this->payment]);
    }
}
