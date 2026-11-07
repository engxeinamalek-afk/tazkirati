<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Models\Concerns\HasImage;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Override;

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

    public function casts(){
        return [
            'status' => EventStatus::class,
        ];
    }
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
    public function tickets():HasManyThrough{
        return $this->hasManyThrough(Ticket::class, Booking::class);
    }
    public function venue():BelongsTo{
        return $this->belongsTo(Venue::class);
    }
    #[Override]
    protected static function booted()
    {
        static::addGlobalScope(new PublishedScope);
    }
}
