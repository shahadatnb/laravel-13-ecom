<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:product_attributes,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_attributes,slug'],
            'type' => ['required', 'in:select,text,color'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_required' => ['nullable', 'boolean'],
            'is_filterable' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'values' => ['nullable', 'array'],
            'values.*.value' => ['nullable', 'string', 'max:255'],
            'values.*.color_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The attribute name is required.',
            'name.unique' => 'The attribute name has already been taken.',
            'type.required' => 'The attribute type is required.',
        ];
    }
}
