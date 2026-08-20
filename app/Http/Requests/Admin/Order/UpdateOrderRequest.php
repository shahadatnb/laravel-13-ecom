<?php

namespace App\Http\Requests\Admin\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:'.implode(',', [
                Order::STATUS_DRAFT,
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_PROCESSING,
                Order::STATUS_PACKED,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
                Order::STATUS_RETURNED,
                Order::STATUS_REFUNDED,
                Order::STATUS_FAILED,
            ])],
            'payment_status' => ['nullable', 'in:'.implode(',', [
                Order::PAYMENT_PENDING,
                Order::PAYMENT_PAID,
                Order::PAYMENT_PARTIALLY_PAID,
                Order::PAYMENT_FAILED,
                Order::PAYMENT_REFUNDED,
                Order::PAYMENT_CANCELLED,
            ])],
            'shipping_status' => ['nullable', 'in:'.implode(',', [
                Order::SHIPPING_PENDING,
                Order::SHIPPING_PROCESSING,
                Order::SHIPPING_PACKED,
                Order::SHIPPING_HANDED_TO_COURIER,
                Order::SHIPPING_IN_TRANSIT,
                Order::SHIPPING_DELIVERED,
                Order::SHIPPING_FAILED,
                Order::SHIPPING_RETURNED,
            ])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid order status selected.',
            'payment_status.in' => 'Invalid payment status selected.',
            'shipping_status.in' => 'Invalid shipping status selected.',
            'admin_notes.max' => 'Admin notes cannot exceed 1000 characters.',
        ];
    }
}
