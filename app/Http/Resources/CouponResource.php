<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'max_discount' => $this->max_discount ? (float) $this->max_discount : null,
            'min_order_amount' => $this->min_order_amount ? (float) $this->min_order_amount : null,
            'max_order_amount' => $this->max_order_amount ? (float) $this->max_order_amount : null,
            'usage_limit' => $this->usage_limit,
            'per_user_limit' => $this->per_user_limit,
            'total_used' => $this->total_used,
            'remaining_usage' => $this->usage_limit ? max(0, $this->usage_limit - $this->total_used) : null,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'priority' => $this->priority,
            'scope' => $this->scope,
            'is_auto_apply' => $this->is_auto_apply,
            'is_first_order_only' => $this->is_first_order_only,
            'is_guest_allowed' => $this->is_guest_allowed,
            'customer_restriction' => $this->customer_restriction,
            'valid_from' => $this->valid_from?->toISOString(),
            'valid_until' => $this->valid_until?->toISOString(),
            'is_valid' => $this->isValid(),
            'has_remaining_usage' => $this->hasRemainingUsage(),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'categories' => $this->whenLoaded('categories'),
            'customers' => $this->whenLoaded('customers'),
            'usages_count' => $this->whenCounted('usages'),
            'last_used_at' => $this->last_used_at?->diffForHumans(),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'id' => $this->createdBy?->id,
                'name' => $this->createdBy?->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
