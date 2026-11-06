<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
// اهمية متوسطة
class MakeQRCode implements ShouldQueue
{
    public $tries=3;
    public $timeout=30;
    public function backoff():array{return [30,60];}
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
    public function viaQueue(){
        return 'default';
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
