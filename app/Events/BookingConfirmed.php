<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class BookingConfirmed
{
    use Dispatchable;

    public function __construct(public readonly int $bookingId) {}
}
