<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cart_id'            => 'required|string|uuid',
            'shipping_full_name' => 'required|string|max:255',
            'shipping_address'   => 'required|string|max:500',
            'shipping_city'      => 'required|string|max:255',
            'shipping_country'   => 'required|string|max:255',
            'payment_method'     => 'required|string|in:bank_transfer',
        ];
    }
}
