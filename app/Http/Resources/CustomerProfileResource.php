<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customer = $this->customer;

        return [
            'id' => $this->id,
            'customer_code' => $this->customer_code,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'marketing_opt_in' => $this->marketing_opt_in,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'notes' => $this->notes,
            'customer' => [
                'id' => $customer?->id,
                'name' => $customer?->name,
                'email' => $customer?->email,
                'phone' => $customer?->phone,
                'avatar' => $customer?->avatar,
                'created_at' => $customer?->created_at?->toIso8601String(),
            ],
            'wallet' => $this->when($this->relationLoaded('customer') && $customer?->relationLoaded('wallet') && $customer->wallet, function () use ($customer) {
                return [
                    'id' => $customer->wallet->id,
                    'balance' => $customer->wallet->balance,
                    'available_balance' => $customer->wallet->available_balance,
                    'status' => $customer->wallet->status,
                ];
            }),
            'addresses' => $this->when($this->relationLoaded('customer') && $customer?->relationLoaded('addresses') && $customer->addresses->isNotEmpty(), function () use ($customer) {
                return UserAddressResource::collection($customer->addresses);
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'banned' => 'Banned',
            default => ucfirst($this->status),
        };
    }
}
