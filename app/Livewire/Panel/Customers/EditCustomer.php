<?php

namespace App\Livewire\Panel\Customers;

use App\Helpers\LivewireHelper;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditCustomer extends Component
{
    use LivewireHelper;

    public $customerId;
    public $name;
    public $email;
    public $phone;

    public function mount()
    {
        $this->customerId = session('customer_id');

        $customer = Customer::findOrFail($this->customerId);
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
    }

    #[Layout('layouts.admin.panel'), Title('Edit Customer')]
    public function render()
    {
        return view('livewire.panel.customers.edit-customer');
    }

    public function update()
    {
        $request = new CustomerRequest();
        $request->merge(['customer_id' => $this->customerId]);

        $rules = $request->rules();
        $messages = $request->messages();

        $data = Validator::make([
            'customer_id' => $this->customerId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ], $rules, $messages)->validate();

        $service = $this->setService('CustomerService');
        $customer = $service->update($data, $this->customerId);

        if ($customer) {
            $this->alertMessage(__('Customer updated successfully.'), 'success');
            return redirect()->route('admin.panel.customers.list');
        }

        $this->alertMessage(__('An error occurred while updating the customer.'), 'error');
        return false;
    }
}
