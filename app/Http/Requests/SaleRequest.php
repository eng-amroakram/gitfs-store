<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        $saleId = $this->route('sale_id') ?? $this->sale_id ?? null;

        return [
            'customer_id'    => ['nullable', 'exists:customers,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sales', 'invoice_number')->ignore($saleId),
            ],
            'total'          => ['required', 'numeric', 'min:0'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'grand_total'    => ['required', 'numeric', 'min:0'],
            'status'         => ['required', Rule::in(['paid', 'partial', 'unpaid'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'items'          => ['required', 'array', 'min:1'], // قائمة الأصناف
            'items.*.item_id'  => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price'    => ['required', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('User is required.'),
            'user_id.exists'   => __('Selected user does not exist.'),
            'invoice_number.required' => __('Invoice number is required.'),
            'invoice_number.unique'   => __('Invoice number already exists.'),
            'total.required'    => __('Total is required.'),
            'grand_total.required' => __('Grand total is required.'),
            'status.required'   => __('Status is required.'),
            'items.required'    => __('At least one item must be added to the sale.'),
            'items.*.item_id.required'  => __('Item is required.'),
            'items.*.item_id.exists'    => __('Selected item does not exist.'),
            'items.*.quantity.required' => __('Quantity is required.'),
            'items.*.quantity.min'      => __('Quantity must be at least 1.'),
            'items.*.price.required'    => __('Price is required.'),
            'items.*.subtotal.required' => __('Subtotal is required.'),
        ];
    }
}
