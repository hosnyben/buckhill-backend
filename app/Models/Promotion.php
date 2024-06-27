<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Storage;

class Promotion extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $fillable = ['title', 'content', 'metdata'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Retrieve promotion by uuid
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $appends = ['cover'];

    // Get cover attribute
    public function getCoverAttribute(): ?string
    {
        if( !$this->metadata )
            return null;

        if( !array_key_exists('image', $this->metadata) )
            return null;

        $file = \App\Models\File::where('uuid', $this->metadata['image'])->first();

        if( !$file )
            return null;

        return Storage::disk('files')->url($file->path);
    }
}
