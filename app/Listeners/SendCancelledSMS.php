<?php

namespace App\Listeners;

use App\Events\EventCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendCancellationSmsJob;
use App\Models\Event;
use App\Models\Scopes\PublishedScope;

class SendCancelledSMS
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
        $eventDetails = Event::withoutGlobalScope(PublishedScope::class)
                            ->select('id', 'title')
                            ->with(['bookings' => function ($query) {
                                        $query->select('id', 'user_id', 'event_id', 'status')
                                            ->where('status', 'paid');
                                        },
                                    'bookings.user:id,name,phone']) 
                            ->findOrFail($event->eventId);

        foreach ($eventDetails->bookings as $booking) {
            if ($booking->user && $booking->user->phone) {
                SendCancellationSmsJob::dispatch(
                    $booking->user->phone, 
                    $booking->user->name, 
                    $eventDetails->title
                );
            }
        }
    }
}
