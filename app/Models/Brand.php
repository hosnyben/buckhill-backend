<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $fillable = ['title', 'slug'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected static function booted()
    {
        static::creating(function ($brand) {
            // if slug is missing on creation, generate it
            if(empty($brand->slug)) {
                $brand->slug = Str::slug($brand->title.'-'.now()->format('Y-m-d').'-'.rand(1000, 9999));
            }
        });
    }

    // Retrieve brand by slug
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
