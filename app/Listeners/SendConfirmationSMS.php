<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Services\SMSProvider\SMSProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfirmationSMS implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SMSProviderInterface $service)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingConfirmed $event): void
    {
        $booking = Booking::with('user:phone')->findOrFail($event->bookingId);
        $this->service->send($booking->user->phone, "Your booking has been confirmed. Booking ID: {$booking->id}");
    }
}
