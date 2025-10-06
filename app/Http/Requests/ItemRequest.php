<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('customer_id') ?? $this->item_id ?? null; // ID إذا موجود للتعديل

        return [
            'name'            => ['required', 'string', 'max:255'],
            'code'            => ['required', 'string', 'max:50', 'unique:items,code,' . $itemId],
            'description'     => ['nullable', 'string'],
            'purchase_price'  => ['required', 'numeric', 'min:0'],
            'sale_price'      => ['required', 'numeric', 'min:0'],
            'quantity'        => ['required', 'integer', 'min:0'],
            'type'            => ['required', 'in:sale,rental'],
            'low_stock_alert' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'اسم الصنف مطلوب',
            'code.required'           => 'كود الصنف مطلوب',
            'code.unique'             => 'الكود مستخدم بالفعل',
            'purchase_price.required' => 'سعر الشراء مطلوب',
            'sale_price.required'     => 'سعر البيع مطلوب',
            'quantity.required'       => 'الكمية مطلوبة',
            'type.required'           => 'نوع الصنف مطلوب',
            'low_stock_alert.required' => 'تنبيه الحد الأدنى مطلوب',
        ];
    }
}
