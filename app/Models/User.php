<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\JWTAuthTrait;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use JWTAuthTrait;
    use HasUuids;

    public string $token = '';
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'phone_number',
        'avatar_uuid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'is_admin',
        'is_marketing',
        'email_verified_at',
        'password',
        'created_at',
        'updated_at',
        'last_login_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Retrieve user by uuid
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Order>
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<File, User>
     */
    public function avatar(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'avatar_uuid', 'uuid');
    }
}
