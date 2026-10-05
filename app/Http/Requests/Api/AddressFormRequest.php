<?php

namespace App\Http\Requests\Api;

use App\Models\UserAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'address_type' => ['required', 'string', Rule::in([UserAddress::TYPE_HOME, UserAddress::TYPE_WORK, UserAddress::TYPE_OTHER])],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'address_type.required' => 'The address type field is required.',
            'address_type.in' => 'The selected address type is invalid.',
            'recipient_name.required' => 'The recipient name field is required.',
            'phone.required' => 'The phone field is required.',
            'address_line_1.required' => 'The address line 1 field is required.',
            'city.required' => 'The city field is required.',
            'country.required' => 'The country field is required.',
            'is_default.boolean' => 'The default field must be true or false.',
            'is_active.boolean' => 'The active field must be true or false.',
        ];
    }
}
