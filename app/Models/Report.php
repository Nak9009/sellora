<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'reporter_id', 'reason', 'description', 'status'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
