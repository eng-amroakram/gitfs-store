<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier') ?? $this->supplier_id ?? null;

        return [
            'name' => ['required', 'max:255'],
            'email' => ['nullable', 'email', 'unique:suppliers,email' . ($supplierId ? ',' . $supplierId : '')],
            'phone' => ['nullable', 'max:15', 'unique:suppliers,phone' . ($supplierId ? ',' . $supplierId : '')],
            'address' => ['nullable', 'max:500'],
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
            'address.max' => 'يجب ألا يزيد العنوان عن 500 حرف',
        ];
    }
}
