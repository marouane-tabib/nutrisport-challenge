<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'total'           => (float) $this->total,
            'status'          => $this->status->value,
            'payment_method'  => $this->payment_method->value,
            'shipping'        => [
                'full_name' => $this->shipping_full_name,
                'address'   => $this->shipping_address,
                'city'      => $this->shipping_city,
                'country'   => $this->shipping_country,
            ],
            'items'           => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at'      => $this->created_at->toISOString(),
        ];
    }
}
