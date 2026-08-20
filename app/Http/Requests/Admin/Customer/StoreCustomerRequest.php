<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date'],
            'marketing_opt_in' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'banned'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The customer name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'gender.in' => 'Gender must be male, female, or other.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'marketing_opt_in.boolean' => 'Marketing opt-in must be true or false.',
            'status.in' => 'Status must be active, inactive, or banned.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
