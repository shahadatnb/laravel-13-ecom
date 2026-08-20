<?php

namespace App\Http\Requests\Admin;

use App\Models\DeliveryZone;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deliveryZone = $this->route('delivery_zone') instanceof DeliveryZone
            ? $this->route('delivery_zone')
            : DeliveryZone::findOrFail($this->route('delivery_zone'));

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:inside_dhaka,outside_dhaka'],
            'districts' => ['required', 'array', 'min:1'],
            'districts.*' => ['required', 'string', 'max:100', 'distinct'],
            'charge' => ['required', 'numeric', 'min:0'],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The zone name is required.',
            'districts.required' => 'Please select at least one district.',
            'districts.min' => 'Please select at least one district.',
            'districts.*.distinct' => 'Duplicate districts are not allowed.',
            'charge.required' => 'The delivery charge is required.',
            'charge.numeric' => 'The delivery charge must be a number.',
        ];
    }
}
