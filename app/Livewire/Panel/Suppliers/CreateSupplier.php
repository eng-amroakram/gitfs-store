<?php

namespace App\Livewire\Panel\Suppliers;

use App\Helpers\LivewireHelper;
use App\Http\Requests\SupplierRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateSupplier extends Component
{
    use LivewireHelper;

    public $name;
    public $email;
    public $phone;
    public $address;

    #[Layout('layouts.admin.panel'), Title('انشاء عميل')]
    public function render()
    {
        return view('livewire.panel.suppliers.create-supplier');
    }

    public function create()
    {
        $request = new SupplierRequest();
        $rules = $request->rules();
        $messages = $request->messages();

        $data = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ], $rules, $messages)->validate();

        $service = $this->setService('SupplierService');
        $supplier = $service->store($data);

        if ($supplier) {
            $this->alertMessage(__('Supplier created successfully.'), 'success');
            return redirect()->route('admin.panel.suppliers.list');
        }

        $this->alertMessage(__('An error occurred while creating the supplier.'), 'error');
        return false;
    }
}
