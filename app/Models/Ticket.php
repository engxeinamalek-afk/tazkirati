<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'event_ticket_type_id',
        'ticket_code',
    ];

    public function scopeForTicketTypes(Builder $query, array $ticketTypeIds): void
    {
        $query->whereIn('event_ticket_type_id', $ticketTypeIds);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
