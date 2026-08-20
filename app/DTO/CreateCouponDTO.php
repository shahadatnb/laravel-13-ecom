<?php

namespace App\DTO;

class CreateCouponDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $type,
        public readonly string $discount_type,
        public readonly float $discount_value,
        public readonly ?float $max_discount,
        public readonly ?float $min_order_amount,
        public readonly ?float $max_order_amount,
        public readonly ?int $usage_limit,
        public readonly int $per_user_limit,
        public readonly string $status,
        public readonly int $priority,
        public readonly string $scope,
        public readonly bool $is_auto_apply,
        public readonly bool $is_first_order_only,
        public readonly bool $is_guest_allowed,
        public readonly ?string $customer_restriction,
        public readonly ?string $payment_method,
        public readonly ?string $shipping_method,
        public readonly ?string $valid_from,
        public readonly ?string $valid_until,
        public readonly ?array $settings,
        public readonly ?array $product_ids,
        public readonly ?array $excluded_product_ids,
        public readonly ?array $category_ids,
        public readonly ?array $excluded_category_ids,
        public readonly ?array $customer_ids,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            code: $data['code'] ?? '',
            title: $data['title'],
            description: $data['description'] ?? null,
            type: $data['type'],
            discount_type: $data['discount_type'],
            discount_value: (float) ($data['discount_value'] ?? 0),
            max_discount: isset($data['max_discount']) ? (float) $data['max_discount'] : null,
            min_order_amount: isset($data['min_order_amount']) ? (float) $data['min_order_amount'] : null,
            max_order_amount: isset($data['max_order_amount']) ? (float) $data['max_order_amount'] : null,
            usage_limit: $data['usage_limit'] ?? null,
            per_user_limit: (int) ($data['per_user_limit'] ?? 1),
            status: $data['status'] ?? 'draft',
            priority: (int) ($data['priority'] ?? 0),
            scope: $data['scope'] ?? 'all',
            is_auto_apply: (bool) ($data['is_auto_apply'] ?? false),
            is_first_order_only: (bool) ($data['is_first_order_only'] ?? false),
            is_guest_allowed: (bool) ($data['is_guest_allowed'] ?? false),
            customer_restriction: $data['customer_restriction'] ?? null,
            payment_method: $data['payment_method'] ?? null,
            shipping_method: $data['shipping_method'] ?? null,
            valid_from: $data['valid_from'] ?? null,
            valid_until: $data['valid_until'] ?? null,
            settings: $data['settings'] ?? null,
            product_ids: $data['product_ids'] ?? null,
            excluded_product_ids: $data['excluded_product_ids'] ?? null,
            category_ids: $data['category_ids'] ?? null,
            excluded_category_ids: $data['excluded_category_ids'] ?? null,
            customer_ids: $data['customer_ids'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_discount' => $this->max_discount,
            'min_order_amount' => $this->min_order_amount,
            'max_order_amount' => $this->max_order_amount,
            'usage_limit' => $this->usage_limit,
            'per_user_limit' => $this->per_user_limit,
            'status' => $this->status,
            'priority' => $this->priority,
            'scope' => $this->scope,
            'is_auto_apply' => $this->is_auto_apply,
            'is_first_order_only' => $this->is_first_order_only,
            'is_guest_allowed' => $this->is_guest_allowed,
            'customer_restriction' => $this->customer_restriction,
            'payment_method' => $this->payment_method,
            'shipping_method' => $this->shipping_method,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'settings' => $this->settings,
            'product_ids' => $this->product_ids,
            'excluded_product_ids' => $this->excluded_product_ids,
            'category_ids' => $this->category_ids,
            'excluded_category_ids' => $this->excluded_category_ids,
            'customer_ids' => $this->customer_ids,
        ];
    }
}
