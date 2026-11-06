<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Support\Facades\Log;
use RuntimeException;
// اهمية متوسطة 
class SendConfirmationEmail implements ShouldQueue
{
    public ? int $bookingId= null;
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
        $this->bookingId= $event->bookingId;
        if(config('services.mail.simulate_failure')){
            throw new RuntimeException("Simulated email failure");
        }else{
            $booking = Booking::with('user:name,email')->findOrFail($event->bookingId);

            Mail::raw("Hello {$booking->user->name}, your booking has been confirmed. Booking ID: {$booking->id}", function ($message) use ($booking) {
                $message->to($booking->user->email)
                    ->subject('Confirmation of Booking');
            });
        }

    }
    public function failed(Throwable $exception){     
        Log::error('Confirmation Email failure', [
            'booking_id' => $this->bookingId,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
