<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerProfile = $this->route('customer');
        $customerId = $customerProfile?->customer_id;

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customerId)],
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
