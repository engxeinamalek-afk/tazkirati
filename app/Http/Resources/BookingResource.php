<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'event_id' => $this->event_id,
            'total_price' => $this->total_price,
            'total_tickets' => $this->total_tickets,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')) 
        ];
    }
}
