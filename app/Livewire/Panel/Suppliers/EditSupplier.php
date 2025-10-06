<?php

namespace App\Livewire\Panel\Suppliers;

use App\Helpers\LivewireHelper;
use App\Http\Requests\SupplierRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditSupplier extends Component
{
    use LivewireHelper;

    public $supplierId;
    public $name;
    public $email;
    public $phone;
    public $address;

    public function mount()
    {
        $this->supplierId = session('supplier_id');

        $service = $this->setService('SupplierService');
        $supplier = $service->model($this->supplierId);

        $this->name = $supplier->name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
    }

    #[Layout('layouts.admin.panel'), Title('تعديل مورد')]
    public function render()
    {
        return view('livewire.panel.suppliers.edit-supplier');
    }

    public function update()
    {
        $request = new SupplierRequest();
        $request->merge(['supplier_id' => $this->supplierId]);

        $rules = $request->rules();
        $messages = $request->messages();

        $data = Validator::make([
            'supplier_id' => $this->supplierId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ], $rules, $messages)->validate();

        $service = $this->setService('SupplierService');
        $supplier = $service->update($data, $this->supplierId);

        if ($supplier) {
            $this->alertMessage(__('Supplier updated successfully.'), 'success');
            return redirect()->route('admin.panel.suppliers.list');
        }

        $this->alertMessage(__('An error occurred while updating the supplier.'), 'error');
        return false;
    }
}
