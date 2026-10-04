<?php

namespace App\Http\Requests\Admin;

use App\Models\DeliveryZoneDistrict;
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
        $district = $this->route('district') instanceof DeliveryZoneDistrict
            ? $this->route('district')
            : DeliveryZoneDistrict::findOrFail($this->route('district'));

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('delivery_zone_districts', 'name')->ignore($district->id),
            ],
            'delivery_zone_id' => ['required', 'exists:delivery_zones,id'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The district name is required.',
            'name.unique' => 'A district with this name already exists.',
            'delivery_zone_id.required' => 'Please select a delivery zone.',
            'delivery_zone_id.exists' => 'The selected delivery zone is invalid.',
            'status.in' => 'The status must be active or inactive.',
        ];
    }
}
