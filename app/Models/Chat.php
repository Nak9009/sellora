<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'user_id', 'seller_id', 'last_message', 'last_message_at'];
    protected $casts = ['last_message_at' => 'datetime'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
