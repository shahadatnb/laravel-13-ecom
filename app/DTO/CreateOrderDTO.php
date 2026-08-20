<?php

namespace App\DTO;

class CreateOrderDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly array $items,
        public readonly ?string $couponCode = null,
        public readonly ?float $discount = 0,
        public readonly ?float $tax = 0,
        public readonly ?float $shippingCharge = 0,
        public readonly ?string $currency = 'BDT',
        public readonly ?string $paymentMethod = null,
        public readonly ?array $shippingAddress = null,
        public readonly ?array $billingAddress = null,
        public readonly ?string $notes = null,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'items' => $this->items,
            'coupon_code' => $this->couponCode,
            'discount' => $this->discount ?? 0,
            'tax' => $this->tax ?? 0,
            'shipping_charge' => $this->shippingCharge ?? 0,
            'currency' => $this->currency ?? 'BDT',
            'payment_method' => $this->paymentMethod,
            'shipping_address' => $this->shippingAddress ? json_encode($this->shippingAddress) : null,
            'billing_address' => $this->billingAddress ? json_encode($this->billingAddress) : null,
            'notes' => $this->notes,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            items: $data['items'],
            couponCode: $data['coupon_code'] ?? null,
            discount: $data['discount'] ?? 0,
            tax: $data['tax'] ?? 0,
            shippingCharge: $data['shipping_charge'] ?? 0,
            currency: $data['currency'] ?? 'BDT',
            paymentMethod: $data['payment_method'] ?? null,
            shippingAddress: $data['shipping_address'] ?? null,
            billingAddress: $data['billing_address'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
