<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'currency', 'method', 'gateway',
        'status', 'transaction_id', 'reference', 'description', 'metadata'
    ];

    protected $casts = ['metadata' => 'json'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
