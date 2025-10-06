<?php

namespace App\Livewire\Panel\Customers;

use App\Helpers\LivewireHelper;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowCustomer extends Component
{
    use LivewireHelper;

    public $customer_id;
    public $customer;

    public function mount()
    {
        $customer_id = session('customer_id');
        if (!$customer_id) {
            return redirect()->route('admin.panel.customers.list');
        }

        $this->customer_id = $customer_id;

        $this->customer = Customer::with([
            'sales' => function ($q) {
                $q->latest();
            },
            'sales.user',
        ])->findOrFail($this->customer_id);
    }


    #[Layout('layouts.admin.panel'), Title('Show Customer')]
    public function render()
    {
        return view('livewire.panel.customers.show-customer', ['customer' => $this->customer]);
    }
}
