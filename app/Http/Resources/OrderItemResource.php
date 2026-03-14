<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id'   => $this->product_id,
            'product_name' => $this->product->name ?? null,
            'quantity'     => $this->quantity,
            'unit_price'   => (float) $this->unit_price,
            'line_total'   => (float) ($this->quantity * $this->unit_price),
        ];
    }
}
