<?php

namespace App\Listeners;

use App\Events\EventCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\CacheBackJob;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Scopes\PublishedScope;
use Illuminate\Support\Facades\DB;
class CacheBack
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EventCancelled $event): void
    {
        $bookings = Booking::select('id', 'user_id', 'event_id', 'status')
            ->where('event_id', $event->eventId)
            ->where('status', 'paid')
            ->get();
        foreach($bookings as $booking){
            CacheBackJob::dispatch($booking);
        }

    }


}
