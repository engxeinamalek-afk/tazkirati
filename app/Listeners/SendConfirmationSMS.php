<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Services\SMSProvider\SMSProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;
use Illuminate\Support\Facades\Log;
// اهمية متوسطة
class SendConfirmationSMS implements ShouldQueue
{
    public ? int $bookingId= null;
    public $tries=3;
    public $timeout=30;
    public function backoff():array{return [30,60];}
    /**
     * Create the event listener.
     */
    public function __construct(protected SMSProviderInterface $service)
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
        $this->bookingId= $event->bookingId;

        $booking = Booking::with('user:id,phone')->findOrFail($event->bookingId);
        $this->service->send($booking->user->phone, "Your booking has been confirmed. Booking ID: {$booking->id}");
    }
    public function failed(BookingConfirmed $event, Throwable $exception): void
    {
        $this->bookingId = $event->bookingId;

        Log::error('Send Confirmation SMS job failed', [
            'booking_id' => $this->bookingId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
