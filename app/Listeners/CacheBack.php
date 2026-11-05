<?php

namespace App\Listeners;

use App\Events\EventCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\CacheBackJob;
use App\Models\Event;
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
        $eventData = Event::select('id', 'title')
                    ->with(['bookings' => function ($query) {
                            $query->select('id', 'user_id', 'event_id', 'status')
                                ->where('status', 'paid');
                            }])
                    ->find($event->eventId);
        $bookings = $eventData->bookings;
        foreach($bookings as $booking){
            CacheBackJob::dispatch($booking);
        }

    }
}
