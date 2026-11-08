<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Throwable;
use Illuminate\Support\Facades\Log;
class EventCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $tries=3;
    public $timeout=60;
    public function backoff():array{return [30,60];}
    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $eventTitle)
    {
        $this->queue='default';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Event Cancelled",
            'message' => "The event '{$this->eventTitle}' that you participated in has been cancelled.",
        ];
    }
    public function failed(?Throwable $exception): void
    {
        Log::error('Send Cancellation Email job failed', [
            'event_title' => $this->eventTitle,
            'error' => $exception?->getMessage(),
        ]);
    }
}
