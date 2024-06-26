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
            'uuid' => $this->uuid,
            'user' => new UserResource($this->user),
            'order_status' => new OrderStatusResource($this->orderStatus),
            'payment' => new PaymentResource($this->payment),
            'products' => ProductResource::collection($this->products),
            'address' => $this->address,
            'delivery_fee' => $this->delivery_fee,
            'amount' => $this->amount,
            'shipping_at' => $this->shipping_at,
        ];
    }
}
