<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        if($booking->wasChanged('status') && ($booking->status === BookingStatus::REFUNDED ||
                                                $booking->status === BookingStatus::EXPIRED)){
            $eventId=$booking->event_id;
            Cache::forget("remaining_event_{$eventId}_tickets");
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        Cache::forget("remaining_event_{$booking->event_id}_tickets");
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
