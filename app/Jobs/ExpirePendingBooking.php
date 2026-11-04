<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ExpirePendingBooking implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $bookingId)
    {
        $this->afterCommit = true;
    }

    public function handle(): void
    {
        DB::transaction(function (): void {
            $booking = Booking::lockForUpdate()->find($this->bookingId);


            if (! $booking || $booking->status !== BookingStatus::PENDING->value) {
                return;
            }

            $booking->tickets()->delete();

            $booking->update([
                'status' => BookingStatus::EXPIRED->value,
            ]);
        }, 3);
    }
}
