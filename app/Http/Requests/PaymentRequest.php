<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        $paymentId = $this->route('payment_id') ?? $this->payment_id ?? null;
        return [
            'payment_reference' => ['required', 'string', 'max:100', 'unique:payments,payment_reference,' . $paymentId],
            'paymentable_type' => ['required', 'in:sale,reservation'],
            'paymentable_id'   => ['required', 'integer'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'method'          => ['required', 'in:cash,card,bank_transfer,palpay,jawwalPay,other'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'paymentable_type.required' => __('Payment type is required.'),
            'paymentable_type.in'       => __('Payment type must be either sale or reservation.'),
            'paymentable_id.required'   => __('Related entity ID is required.'),
            'paymentable_id.integer'    => __('Related entity ID must be an integer.'),
            'amount.required'           => __('Amount is required.'),
            'amount.min'                => __('Amount must be at least 0.01.'),
            'method.required'           => __('Payment method is required.'),
            'method.in'                 => __('Invalid payment method selected.'),
        ];
    }
}
