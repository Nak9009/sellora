<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdImage extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'image_path', 'is_primary'];
    protected $casts = ['is_primary' => 'boolean'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function getFullPathAttribute()
    {
        return config('app.url').'/storage/'.$this->image_path;
    }
}
