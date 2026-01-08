<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'amount' => $this->amount,
            'currency' => $this->currency,
            'method' => $this->method,
            'gateway' => $this->gateway,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'reference' => $this->reference,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toIso8601String(),

            'user' => new UserResource($this->whenLoaded('user')),
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
        ];
    }
}
