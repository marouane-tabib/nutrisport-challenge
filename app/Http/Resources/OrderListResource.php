<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'client_name'      => $this->user->full_name,
            'total'            => (float) $this->total,
            'status'           => $this->status->value,
            'remaining_balance' => (float) $this->remaining_balance,
            'site'             => $this->site->name,
            'created_at'       => $this->created_at->toISOString(),
        ];
    }
}
