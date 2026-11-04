<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Notifications\NewBookingNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
class SendConfirmationNotification implements ShouldQueue
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
    public function handle(BookingConfirmed $event): void
    {
        $booking = Booking::with('event.organizer')->findOrFail($event->bookingId);
        $booking->event->organizer->notify(new NewBookingNotification($booking->id));
    }
}
