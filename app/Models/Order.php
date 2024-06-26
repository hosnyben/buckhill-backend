<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'user_uuid',
        'order_status_id',
        'payment_id',
        'products',
        'address',
        'delivery_fee',
        'amount',
        'shipping_at',
    ];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
        'shipping_at' => 'datetime',
    ];
}
