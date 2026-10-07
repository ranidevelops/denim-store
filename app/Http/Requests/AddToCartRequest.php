<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'size' => ['required', Rule::in(config('denim.sizes'))],
            'color' => ['required', Rule::in(config('denim.colors'))],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'size.required' => 'Please select a size.',
            'size.in' => 'Please select a valid size.',
            'color.required' => 'Please select a color.',
            'color.in' => 'Please select a valid color.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
