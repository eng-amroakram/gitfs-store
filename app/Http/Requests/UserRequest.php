<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->user_id ?? null; // ID إذا موجود للتعديل

        return [
            'name' => ['required'],
            'username' => ['required', 'max:15', 'unique:users,username' . ($userId ? ',' . $userId : '')],
            'email' => ['required', 'email', 'unique:users,email' . ($userId ? ',' . $userId : '')],
            'phone' => ['nullable', 'max:10', 'unique:users,phone' . ($userId ? ',' . $userId : '')],
            'role' => ['required', 'in:admin,cashier,purchaser,inventory_manager,owner'],
            'status' => ['required', 'in:active,inactive'],
            'password' => [$userId ? 'nullable' : 'required', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الحقل مطلوب',
            'username.required' => 'الحقل مطلوب',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل',
            'username.max' => 'يجب ألا يزيد اسم المستخدم عن 15 حرفًا',
            'email.required' => 'الحقل مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'phone.max' => 'يجب ألا يزيد رقم الهاتف عن 10 رقمًا',
            'role.required' => 'الحقل مطلوب',
            'role.in' => 'الدور غير صالح',
            'status.required' => 'الحقل مطلوب',
            'password.required' => 'الحقل مطلوب',
            'password.min' => 'يجب أن يكون الحد الأدنى لطول كلمة المرور 8 أحرف',
        ];
    }
}
