<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
