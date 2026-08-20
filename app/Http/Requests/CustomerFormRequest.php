<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'marketing_opt_in' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive,banned'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'gender.in' => 'Gender must be one of: male, female, other.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'marketing_opt_in.boolean' => 'Marketing opt-in must be true or false.',
            'status.in' => 'Status must be one of: active, inactive, banned.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
