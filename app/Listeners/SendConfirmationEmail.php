<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendConfirmationEmail implements ShouldQueue
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
        $booking = Booking::with('user:name,email')->findOrFail($event->bookingId);

        Mail::raw("Hello {$booking->user->name}, your booking has been confirmed. Booking ID: {$booking->id}", function ($message) use ($booking) {
            $message->to($booking->user->email)
                ->subject('Confirmation of Booking');
        });
    }
}
