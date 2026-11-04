<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class EventTicketType extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'type',
        'price',
        'total_quantity',
    ];

    public function scopeForEvent(Builder $query, int $eventId): void
    {
        $query->where('event_id', $eventId);
    }

    public function scopeForTypes(Builder $query, array $types): void
    {
        $query->whereIn('type', $types);
    }
}
