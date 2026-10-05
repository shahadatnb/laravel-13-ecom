<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'stock_status' => ['nullable', 'string', 'in:in,low,out'],
            'status' => ['nullable', 'string', 'in:published,draft,pending,hidden,archived'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'featured' => ['nullable', 'string', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.max' => 'The search term may not be greater than 255 characters.',
            'stock_status.in' => 'The selected stock filter is invalid.',
            'status.in' => 'The selected status filter is invalid.',
            'brand_id.exists' => 'The selected brand does not exist.',
            'category_id.exists' => 'The selected category does not exist.',
            'featured.in' => 'The selected featured filter is invalid.',
        ];
    }
}
