<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'condition' => $this->condition,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_approved' => $this->is_approved,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => AdImageResource::collection($this->whenLoaded('images')),
            'favorites_count' => $this->whenLoaded('favorites', fn() => $this->favorites->count()),
            'is_favorite' => $this->when(
                auth()->check(),
                fn() => auth()->user()->favorites()->where('ad_id', $this->id)->exists()
            ),
        ];
    }
}
