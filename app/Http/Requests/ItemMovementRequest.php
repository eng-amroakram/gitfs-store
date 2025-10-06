<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemMovementRequest extends FormRequest
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
        return [
            'item_id'       => 'required|exists:items,id',
            'quantity'      => 'required|integer|min:1',
            'movement_type' => 'required|in:in,out',
            'reason'        => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => __('Item is required'),
            'item_id.exists'   => __('Item not found'),
            'quantity.required' => __('Quantity is required'),
            'quantity.integer' => __('Quantity must be an integer'),
            'movement_type.in' => __('Movement type must be in or out'),
        ];
    }
}
