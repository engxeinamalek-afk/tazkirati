<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class MakeQRCode implements ShouldQueue
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
        // محاكاة لانشاء qr
        $booking = Booking::with('tickets')->findOrFail($event->bookingId);

        foreach ($booking->tickets as $ticket) {
            $ticket->update([
                'ticket_code' => 'TCK-' . Str::upper((string) Str::uuid()),
            ]);
        }
    }
}
