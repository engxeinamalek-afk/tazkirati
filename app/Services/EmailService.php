<?php

namespace App\Services;
use App\Jobs\SendPromotionalEmail;
use App\Models\Event;
class EmailService
{
    public function sendEmail(Event $event,string $subject,string $body)
    {   
        $event->bookings()->reservingSeats()
            ->with('user:id,email')
            ->chunkById(100, function ($bookings) use ($subject, $body) {
                foreach ($bookings as $booking) {
                        SendPromotionalEmail::dispatch(
                            $booking->user->email,
                            $subject,
                            $body
                        )->onQueue('emails');
                    }
            });
    }
}
