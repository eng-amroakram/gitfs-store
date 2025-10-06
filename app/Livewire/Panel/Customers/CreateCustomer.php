<?php

namespace App\Livewire\Panel\Customers;

use App\Helpers\LivewireHelper;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateCustomer extends Component
{
    use LivewireHelper;

    public $name;
    public $email;
    public $phone;

    #[Layout('layouts.admin.panel'), Title('انشاء عميل')]
    public function render()
    {
        return view('livewire.panel.customers.create-customer');
    }

    public function create()
    {
        $request = new CustomerRequest();
        $rules = $request->rules();
        $messages = $request->messages();

        $data = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ], $rules, $messages)->validate();

        $service = $this->setService('CustomerService');
        $customer = $service->store($data);

        if ($customer) {
            $this->alertMessage(__('Customer created successfully.'), 'success');
            return redirect()->route('admin.panel.customers.list');
        }

        $this->alertMessage(__('An error occurred while creating the customer.'), 'error');
        return false;
    }
}
