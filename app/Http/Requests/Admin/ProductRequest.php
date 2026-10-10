<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999.99'],
            'status' => ['required', Rule::in(ProductStatus::values())],

            'variants' => ['required', 'array', 'min:1'],
            'variants.*.size' => ['required', Rule::in(config('denim.sizes'))],
            'variants.*.color' => ['required', Rule::in(config('denim.colors'))],
            'variants.*.stock' => ['required', 'integer', 'min:0', 'max:100000'],

            // Images are mandatory only when creating; SVG is excluded on purpose (script injection risk).
            'images' => [$this->isMethod('POST') ? 'required' : 'nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $combinations = collect($this->input('variants', []))
                    ->map(fn ($variant) => ($variant['size'] ?? '').'|'.($variant['color'] ?? ''));

                if ($combinations->count() !== $combinations->unique()->count()) {
                    $validator->errors()->add('variants', 'Each size and color combination can only be added once.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Please upload at least one product image.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPG, PNG or WebP.',
            'images.*.max' => 'Each image must be 2 MB or smaller.',
            'images.*.dimensions' => 'Each image must be at most 6000 x 6000 pixels.',
            'variants.required' => 'Add at least one size and color variant.',
        ];
    }
}
