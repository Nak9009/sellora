<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar',
        'bio',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'rating',
        'is_verified',
        'is_blocked',
        'verification_token',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'verification_token'
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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // Relationships
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
    public function scopeNotBlocked($query)
    {
        return $query->where('is_blocked', false);
    }
    public function scopeActive($query)
    {
        return $query->verified()->notBlocked();
    }

    // Methods
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
