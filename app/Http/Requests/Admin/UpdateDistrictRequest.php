<?php

namespace App\Http\Requests\Admin;

use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $district = $this->route('district') instanceof District
            ? $this->route('district')
            : District::findOrFail($this->route('district'));

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('districts', 'name')->ignore($district->id),
            ],
            'division' => ['nullable', 'string', 'max:100'],
            'delivery_zone_id' => ['nullable', 'exists:delivery_zones,id'],
            'charge' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The district name is required.',
            'name.unique' => 'A district with this name already exists.',
            'delivery_zone_id.exists' => 'The selected delivery zone is invalid.',
            'charge.numeric' => 'The delivery charge must be a number.',
            'charge.min' => 'The delivery charge cannot be negative.',
            'status.in' => 'The status must be active or inactive.',
        ];
    }
}
