<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $fillable = ['name', 'path', 'size', 'type'];
    protected $hidden = ['id', 'created_at', 'updated_at'];
}
