<?php

namespace App\Listeners;

use App\Events\EventCancelled;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendCancellationEmailJob;
class SendCancelledEmail
{
    public function handle(EventCancelled $event): void
    {
        $eventData = Event::select('id', 'title')
                         ->with(['bookings' => function ($query) {
                                    $query->select('id', 'user_id', 'event_id', 'status')
                                        ->where('status', 'paid');
                                    },
                                'bookings.user:id,name,email'])
                         ->find($event->eventId);
        $bookings = $eventData->bookings;
    
        foreach ($bookings as $booking) {
            if ($booking->user) {
                SendCancellationEmailJob::dispatch($booking->user, $eventData->title, $booking->id);
            }
        }   
    }
}
