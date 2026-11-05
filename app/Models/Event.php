<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Event extends Model
{
    use HasFactory;
    use HasImage;

    protected $fillable = [
        'title',
        'venue_id',
        'organizer_id',
        'status',
    ];

    public function TicketTypes()
    {
        return $this->hasMany(EventTicketType::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
