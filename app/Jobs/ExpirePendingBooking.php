<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Str;
use App\Models\User;
// اهمية عالية 
class ExpirePendingBooking implements ShouldQueue
{
    use Queueable;
    public $tries=3;
    public $timeout=30;
    public function backoff():array{return [30,60];}

    public function __construct(public int $bookingId)
    {
        $this->afterCommit = true;
        $this->onQueue('high');

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
        });
    }
    public function failed(Throwable $exception){
        Log::error('Release expired booking job failed', [
            'booking_id' => $this->bookingId,
            'exception' => $exception?->getMessage(),
        ]);
        $admins= User::where('role','admin')->get();
        foreach($admins as $admin){
                $admin->notifications()->create([
                    'id' => (string) Str::uuid(),

                    'type' => 'job_failed',

                    'data' => [
                        'title' => 'Job Failed',
                        'message' => 'Release expired booking job failed',
                        'error' => $exception->getMessage(),
                    ],
                ]);
        }

    }
}
