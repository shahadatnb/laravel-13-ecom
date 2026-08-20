<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => $this->whenLoaded('product', fn () => [
                'id' => $this->product?->id,
                'name' => $this->product?->name,
                'sku' => $this->product?->sku,
            ]),
            'warehouse' => $this->whenLoaded('warehouse', fn () => [
                'id' => $this->warehouse?->id,
                'name' => $this->warehouse?->name,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'type' => $this->type,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'reference_number' => $this->reference_number,
            'quantity_before' => $this->quantity_before,
            'quantity_change' => $this->quantity_change,
            'quantity_after' => $this->quantity_after,
            'unit_cost' => $this->unit_cost ? (float) $this->unit_cost : null,
            'status' => $this->status,
            'reason' => $this->reason,
            'is_inbound' => $this->isInbound(),
            'is_outbound' => $this->isOutbound(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
