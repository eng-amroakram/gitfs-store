<?php

namespace App\Livewire\Panel\Users;

use App\Helpers\LivewireHelper;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateUser extends Component
{
    use LivewireHelper;

    public $name;
    public $username;
    public $email;
    public $phone;
    public $role = 'cashier';
    public $status = 'active';
    public $password;

    #[Layout('layouts.admin.panel'), Title('Create User')]
    public function render()
    {
        return view('livewire.panel.users.create-user');
    }

    public function create()
    {
        // استدعاء القواعد والرسائل من UserRequest
        $request = new UserRequest();
        $rules = $request->rules();
        $messages = $request->messages();

        // دمج بيانات Livewire properties في الـ Request
        $data = Validator::make([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'password' => Hash::make($this->password),
        ], $rules, $messages)->validate();

        // استخدام الخدمة لتخزين البيانات
        $service = $this->setService('UserService');
        $user = $service->store($data);

        if ($user) {
            $this->alertMessage(__('User created successfully.'), 'success');
            return redirect()->route('admin.panel.users.list');
        }

        $this->alertMessage(__('An error occurred while creating the user.'), 'error');
        return false;
    }
}
