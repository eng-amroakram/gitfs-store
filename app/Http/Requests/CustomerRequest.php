<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer_id') ?? $this->customer_id ?? null; // ID إذا موجود للتعديل

        return [
            'name' => ['required', 'max:255'],
            'email' => ['nullable', 'email', 'unique:customers,email' . ($customerId ? ',' . $customerId : '')],
            'phone' => ['nullable', 'max:15', 'unique:customers,phone' . ($customerId ? ',' . $customerId : '')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الحقل مطلوب',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'phone.max' => 'يجب ألا يزيد رقم الهاتف عن 15 رقمًا',
        ];
    }
}
