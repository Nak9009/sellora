<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'ad_id' => $this->ad_id,
            'user_id' => $this->user_id,
            'seller_id' => $this->seller_id,
            'last_message' => $this->last_message,
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),

            'ad' => new AdResource($this->whenLoaded('ad')),
            'user' => new UserResource($this->whenLoaded('user')),
            'seller' => new UserResource($this->whenLoaded('seller')),
            'messages_count' => $this->whenLoaded('messages', fn () => $this->messages->count()),
            'unread_count' => $this->whenLoaded('messages',
                fn () => $this->messages->where('is_read', false)->count()
            ),
        ];
    }
}
