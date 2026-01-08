<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'address' => $this->address,
            'city' => $this->city,
            'rating' => $this->rating,
            'is_verified' => $this->is_verified,
            'is_blocked' => $this->is_blocked,
            'created_at' => $this->created_at->toIso8601String(),

            'active_subscription' => new SubscriptionResource(
                $this->whenLoaded('subscriptions', fn () => $this->subscriptions->where('status', 'active')->first())
            ),
        ];
    }
}
