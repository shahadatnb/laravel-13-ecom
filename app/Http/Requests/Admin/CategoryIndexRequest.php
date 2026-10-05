<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive,archived'],
            'featured' => ['nullable', 'string', 'in:0,1'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.max' => 'The search term may not be greater than 255 characters.',
            'status.in' => 'The selected status filter is invalid.',
            'featured.in' => 'The selected featured filter is invalid.',
            'parent_id.integer' => 'The selected parent category is invalid.',
            'parent_id.exists' => 'The selected parent category does not exist.',
        ];
    }
}
