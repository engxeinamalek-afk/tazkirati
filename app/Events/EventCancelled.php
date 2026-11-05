<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class EventCancelled
{
    use Dispatchable;

    public function __construct(public readonly int $eventId) {}
}
