<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $uuid
 * @property string $user
 * @property string $orderStatus
 * @property string $payment
 * @property string $products
 * @property string $address
 * @property float $delivery_fee
 * @property float $amount
 * @property string $shipping_at
 */
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
            'order_status' => $this->orderStatus,
            'payment' => new PaymentResource($this->payment),
            'products' => $this->products,
            'address' => $this->address,
            'delivery_fee' => $this->delivery_fee,
            'amount' => $this->amount,
            'shipping_at' => $this->shipping_at,
        ];
    }
}
