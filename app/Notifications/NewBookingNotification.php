<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    public function __construct(public readonly int $bookingId) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->bookingId,
            'message' => "Booking confirmed for booking ID: {$this->bookingId}",
        ];
    }
}
