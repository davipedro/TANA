<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'user_id' => $this->user_id,
            'table_id' => $this->table_id,
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'guest_phone' => $this->guest_phone,
            'reservation_datetime' => $this->reservation_datetime,
            'party_size' => $this->party_size,
            'duration' => $this->duration,
            'notes' => $this->notes,
            'status' => $this->status,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at,
            'reminder_sent_at' => $this->reminder_sent_at,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'can_be_cancelled' => $this->canBeCancelled(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'restaurant' => $this->when($this->relationLoaded('restaurant'), fn() => $this->restaurant?->toArray()),
            'user' => $this->when($this->relationLoaded('user'), fn() => $this->user?->toArray()),
            'table' => $this->when($this->relationLoaded('table'), fn() => $this->table?->toArray()),
        ];
    }
}
