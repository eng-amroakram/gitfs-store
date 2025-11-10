<?php

namespace App\Livewire\Panel\Auth;

use App\Helpers\LivewireHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    use LivewireHelper;

    public $username;
    public $password;
    public $remember = true;

    #[Layout('layouts.admin.auth.login', ['headerTitle' => 'تسجيل الدخول']), Title('تسجيل الدخول')]
    public function render()
    {
        return view('livewire.panel.auth.login');
    }

    public function login()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'الرجاء إدخال اسم المستخدم',
            'password.required' => 'الرجاء إدخال كلمة المرور',
        ]);

        $user = User::where('username', $this->username)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            $this->alertMessage('خطأ في اسم المستخدم أو كلمة المرور', 'error');
            return null;
        }

        dd($user);

        Auth::login($user, $this->remember);
        return redirect()->route('admin.panel.dashboard');
    }
}
