<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $reservationId = $this->route('reservation_id') ?? $this->reservation_id ?? null;

        return [
            'reservation_number' => ['required', 'string', 'max:255', 'unique:reservations,reservation_number,' . $reservationId],
            'customer_id' => ['nullable', 'exists:customers,id'],
            // 'user_id'     => ['required', 'exists:users,id'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'deposit'     => ['nullable', 'numeric', 'min:0'],
            'total'       => ['required', 'numeric', 'min:0'],
            'remaining'   => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,completed,cancelled'],
            'description' => ['nullable', 'string', 'max:1000'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'items'       => ['required', 'array', 'min:1'], // قائمة الأصناف
            'items.*.item_id'  => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price'    => ['required', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'user_id.required' => __('User is required.'),
            // 'user_id.exists'   => __('Selected user does not exist.'),
            'start_date.required' => __('Start date is required.'),
            'start_date.date'     => __('Start date must be a valid date.'),
            'end_date.required'   => __('End date is required.'),
            'end_date.date'       => __('End date must be a valid date.'),
            'end_date.after_or_equal' => __('End date must be a date after or equal to start date.'),
            'deposit.numeric'     => __('Deposit must be a numeric value.'),
            'total.required'      => __('Total is required.'),
            'remaining.required'  => __('Remaining amount is required.'),
            'status.required'     => __('Status is required.'),
            'status.in'           => __('Status must be one of the following: active, completed, cancelled.'),
            'items.required'      => __('At least one item must be added to the reservation.'),
            'items.*.item_id.required'  => __('Item is required.'),
            'items.*.item_id.exists'    => __('Selected item does not exist.'),
            'items.*.quantity.required' => __('Quantity is required.'),
            'items.*.quantity.min'      => __('Quantity must be at least 1.'),
            'items.*.price.required'    => __('Price is required.'),
            'items.*.subtotal.required' => __('Subtotal is required.'),
        ];
    }
}
