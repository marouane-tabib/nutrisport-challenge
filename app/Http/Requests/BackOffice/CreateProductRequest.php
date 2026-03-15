<?php

namespace App\Http\Requests\BackOffice;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middleware handles authentication
    }

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
