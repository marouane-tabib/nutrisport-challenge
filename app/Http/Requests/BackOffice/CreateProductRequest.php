<?php

namespace App\Http\Requests\BackOffice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'name'              => 'required|string|max:255',
            'stock'             => 'required|integer|min:0',
            'prices'            => 'required|array|min:1',
            'prices.*.site_id'  => 'required|integer|exists:sites,id',
            'prices.*.price'    => 'required|numeric|min:0',
        ];
    }
}
