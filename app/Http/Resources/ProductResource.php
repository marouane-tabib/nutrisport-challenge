<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'stock'     => $this->stock,
            'in_stock'  => $this->stock > 0,
            'price'     => $this->when(isset($this->price), $this->price), // for client catalog
            'prices'    => $this->whenLoaded('prices', function () {
                return $this->prices->map(fn($price) => [
                    'site_id' => $price->site_id,
                    'price'   => (float) $price->price,
                ]);
            }),
        ];
    }
}
