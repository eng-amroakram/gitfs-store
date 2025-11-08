<?php

namespace App\Livewire\Panel\Users;

use App\Helpers\LivewireHelper;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditUser extends Component
{
    use LivewireHelper;

    public $user;
    public $userId;
    public $name;
    public $username;
    public $email;
    public $phone;
    public $role;
    public $status;
    public $password;

    public function mount()
    {
        $this->userId = session('user_id');

        $user = User::find($this->userId);
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->role;
        $this->status = $user->status;
    }

    #[Layout('layouts.admin.panel'), Title('Edit User')]
    public function render()
    {
        return view('livewire.panel.users.edit-user');
    }

    public function update()
    {
        $request = new UserRequest();

        // نضيف user_id مباشرة للفورم
        $request->merge(['user_id' => $this->userId]);

        $rules = $request->rules();
        $messages = $request->messages();

        // دمج user_id للتعديل

        $data = [
            'user_id' => $this->userId,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $dataRes = Validator::make($data, $rules, $messages)->validate();

        $service = $this->setService('UserService');
        $user = $service->update($dataRes, $this->userId);

        if ($user) {
            $this->alertMessage(__('User updated successfully.'), 'success');
            return redirect()->route('admin.panel.users.list');
        }

        $this->alertMessage(__('An error occurred while updating the user.'), 'error');
        return false;
    }
}
