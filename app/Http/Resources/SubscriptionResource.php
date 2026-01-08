<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'plan_id' => $this->plan_id,
            'status' => $this->status,
            'started_at' => $this->started_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'auto_renew' => $this->auto_renew,
            'is_active' => $this->isActive(),

            'plan' => new PlanResource($this->whenLoaded('plan')),
        ];
    }
}
