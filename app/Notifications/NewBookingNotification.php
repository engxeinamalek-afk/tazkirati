<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Throwable;
use Illuminate\Support\Facades\Log;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $tries=3;
    public $timeout=30;
    public function backoff():array{return [30,60];}
    
    public function __construct(public readonly int $bookingId) {
        $this->queue = 'default';
    }

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
    public function failed(?Throwable $exception): void
    {
        Log::error('Send Cancellation Email job failed', [
            'bookung_id' => $this->bookingId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
