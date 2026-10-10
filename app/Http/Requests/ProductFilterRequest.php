<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
{
    public const SORTS = [
        'newest' => 'Newest',
        'price_asc' => 'Price: low to high',
        'price_desc' => 'Price: high to low',
        'name' => 'Name',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', Rule::exists('categories', 'slug')->whereNull('deleted_at')],
            'size' => ['nullable', Rule::in(config('denim.sizes'))],
            'color' => ['nullable', Rule::in(config('denim.colors'))],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', Rule::when($this->filled('min_price'), ['gte:min_price'])],
            'sort' => ['nullable', Rule::in(array_keys(self::SORTS))],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'max_price.gte' => 'The maximum price must be greater than or equal to the minimum price.',
        ];
    }
}
