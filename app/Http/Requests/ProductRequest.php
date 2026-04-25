<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Normalize incoming payload before validation rules are applied.
     */
    protected function prepareForValidation(): void
    {
        $categories = $this->input('categories');

        if (is_string($categories)) {
            $decoded = json_decode($categories, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['categories' => $decoded]);
            }
        }
    }

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

            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'required|image',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ];
    }
}
