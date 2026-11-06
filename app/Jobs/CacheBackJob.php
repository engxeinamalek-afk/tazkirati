<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Enums\BookingStatus;
use App\Models\User;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
// اهمية عالية 
class CacheBackJob implements ShouldQueue
{
    use Queueable;

    public $tries=3;
    public $timeout=60;
    public function backoff():array{return [30,60];}
    /**
     * Create a new job instance.
     */
    public function __construct(protected Booking $booking)
    {
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // محاكاة لترجيع المصاري
        $this->booking->update(['status' => BookingStatus::REFUNDED->value]);
    }
    public function failed(Throwable $exception){     
        Log::error('Cache back failure', [
            'booking_id' => $this->booking->id,
            'exception' => $exception?->getMessage(),
        ]);
        $admins= User::where('role','admin')->get();
        foreach($admins as $admin){
                $admin->notifications()->create([
                    'id' => (string) Str::uuid(),

                    'type' => 'job_failed',

                    'data' => [
                        'title' => 'Job Failed',
                        'message' => "Cache back failure for booking {$this->booking->id}",
                        'error' => $exception->getMessage(),
                    ],
                ]);
        }
    }
}
