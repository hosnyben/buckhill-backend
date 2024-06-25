<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $fillable = ['title', 'slug', 'content', 'metdata'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Retrieve post by uuid
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
