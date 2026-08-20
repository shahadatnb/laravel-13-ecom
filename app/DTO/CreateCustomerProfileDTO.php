<?php

namespace App\DTO;

class CreateCustomerProfileDTO
{
    public function __construct(
        public readonly int $customerId,
        public readonly ?string $gender = null,
        public readonly ?string $dateOfBirth = null,
        public readonly bool $marketingOptIn = false,
        public readonly string $status = 'active',
        public readonly ?string $notes = null,
    ) {}

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'customer_code' => $this->generateCustomerCode(),
            'gender' => $this->gender,
            'date_of_birth' => $this->dateOfBirth,
            'marketing_opt_in' => $this->marketingOptIn,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }

    private function generateCustomerCode(): string
    {
        return 'CUST-'.strtoupper(substr(uniqid(), -6));
    }

    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['customer_id'],
            gender: $data['gender'] ?? null,
            dateOfBirth: $data['date_of_birth'] ?? null,
            marketingOptIn: $data['marketing_opt_in'] ?? false,
            status: $data['status'] ?? 'active',
            notes: $data['notes'] ?? null,
        );
    }
}
