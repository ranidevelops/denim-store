<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $current = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($current),
                // Different names can collapse to the same slug ("Jeans" vs "Jeans!").
                function (string $attribute, mixed $value, Closure $fail) use ($current) {
                    $slug = Str::slug($value);

                    if ($slug === '') {
                        $fail('The name must contain letters or numbers.');

                        return;
                    }

                    $taken = Category::withTrashed()
                        ->where('slug', $slug)
                        ->when($current, fn ($query) => $query->whereKeyNot($current->getKey()))
                        ->exists();

                    if ($taken) {
                        $fail('A category with a very similar name already exists.');
                    }
                },
            ],
        ];
    }
}
