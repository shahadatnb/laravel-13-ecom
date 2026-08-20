<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'shipping_charge' => (float) $this->shipping_charge,
            'grand_total' => (float) $this->grand_total,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => (float) $this->due_amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->getPaymentStatusLabel(),
            'payment_method' => $this->payment_method,
            'shipping_status' => $this->shipping_status,
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'coupon_code' => $this->coupon_code,
            'coupon_discount' => (float) $this->coupon_discount,
            'notes' => $this->notes,
            'admin_notes' => $this->admin_notes,
            'customer' => $this->when($this->relationLoaded('customer'), function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'status_histories' => $this->when($this->relationLoaded('statusHistories'), function () {
                return $this->statusHistories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'from_status' => $history->from_status,
                        'to_status' => $history->to_status,
                        'changed_by_type' => $history->changed_by_type,
                        'notes' => $history->notes,
                        'created_at' => $history->created_at?->toIso8601String(),
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
