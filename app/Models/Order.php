<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return BelongsTo<OrderStatus, Order>
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * @return BelongsTo<Payment, Order>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
