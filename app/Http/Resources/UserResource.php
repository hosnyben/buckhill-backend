<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $uuid
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $address
 * @property string $phone_number
 * @property bool $is_marketing
 * @property \App\Models\File|null $avatar
 * @property string|null $token
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'is_marketing' => $this->is_marketing,
            'avatar' => $this->avatar ? $this->avatar->path : null,
        ];

        if($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}
