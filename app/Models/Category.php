<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->uuid = (string) Str::uuid(); // Automatically generate and set the UUID

            // if slug is missing on creation, generate it
            if(empty($category->slug)) {
                $category->slug = Str::slug($category->title);
            }
        });
    }

    // Retrieve category by slug
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Prevent slug to be in a different format
    public function setSlugAttribute(string $value): void
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
