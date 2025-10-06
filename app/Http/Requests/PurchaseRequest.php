<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
        $purchaseId = $this->route('purchase_id') ?? $this->purchase_id ?? null; // ID إذا موجود للتعديل

        return [
            'invoice_number' => 'required|string|max:255|unique:purchases,invoice_number,' . ($purchaseId ? $purchaseId : 'NULL') . ',id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'user_id'        => 'required|exists:users,id',
            'total'          => 'required|numeric|min:0',

            'items'                 => 'required|array|min:1',
            'items.*.item_id'       => 'required|exists:items,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.price'         => 'required|numeric|min:0',
            'items.*.subtotal'      => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.required' => __('Invoice number is required.'),
            'invoice_number.unique'   => __('Invoice number must be unique.'),
            'supplier_id.required'    => __('Supplier is required.'),
            'supplier_id.exists'      => __('Supplier does not exist.'),
            'user_id.required'        => __('User is required.'),
            'user_id.exists'          => __('User does not exist.'),
            'total.required'          => __('Total is required.'),

            'items.required'          => __('At least one item is required.'),
            'items.*.item_id.required' => __('Item is required.'),
            'items.*.item_id.exists'  => __('Selected item does not exist.'),
            'items.*.quantity.required' => __('Quantity is required.'),
            'items.*.quantity.min'    => __('Quantity must be at least 1.'),
            'items.*.price.required'  => __('Price is required.'),
            'items.*.subtotal.required' => __('Subtotal is required.'),
        ];
    }
}
