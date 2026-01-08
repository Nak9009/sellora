<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Ad extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'price', 'currency', 'condition', 'location', 'latitude', 'longitude',
        'status', 'is_featured', 'is_approved', 'rejection_reason',
        'views_count', 'promoted_until'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'promoted_until' => 'datetime',
    ];

    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function images() { return $this->hasMany(AdImage::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function reports() { return $this->hasMany(Report::class); }
    public function chats() { return $this->hasMany(Chat::class); }

    // Scopes
    public function scopeApproved($query) { return $query->where('is_approved', true); }
    public function scopePending($query) { return $query->where('is_approved', false); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }

    // Get slug options
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    // Methods
    public function incrementViews() { $this->increment('views_count'); }
    public function isFeatured() { return $this->is_featured && $this->promoted_until > now(); }
}
