<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_sku' => ['nullable', 'string', 'max:100'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0'],
            'items.*.variant_attributes' => ['nullable', 'array'],

            'coupon_code' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'shipping_charge' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'payment_method' => ['nullable', 'string', 'max:50'],

            'shipping_address' => ['nullable', 'array'],
            'shipping_address.recipient_name' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.phone' => ['required_with:shipping_address', 'string', 'max:20'],
            'shipping_address.address_line_1' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.city' => ['required_with:shipping_address', 'string', 'max:100'],
            'shipping_address.state' => ['nullable', 'string', 'max:100'],
            'shipping_address.postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_address.country' => ['nullable', 'string', 'max:100'],

            'billing_address' => ['nullable', 'array'],
            'billing_address.recipient_name' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.phone' => ['required_with:billing_address', 'string', 'max:20'],
            'billing_address.address_line_1' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.city' => ['required_with:billing_address', 'string', 'max:100'],

            'notes' => ['nullable', 'string', 'max:1000'],
            'referrer_code' => ['nullable', 'string', 'max:20'],
            'referrer_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Order must contain at least one item.',
            'items.min' => 'Order must contain at least one item.',
            'items.*.product_id.required' => 'Product ID is required.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
            'items.*.product_name.required' => 'Product name is required.',
            'items.*.unit_price.required' => 'Unit price is required.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
