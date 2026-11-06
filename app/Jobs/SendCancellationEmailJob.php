<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Throwable;
use Illuminate\Support\Facades\Log;
use RuntimeException;
// اهمية متوسطة
class SendCancellationEmailJob implements ShouldQueue
{
    use Queueable;
    public $tries=4;
    public $timeout=30;
    public function backoff():array{return [30,60,120];}
    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user,
                                protected string $eventTitle,
                                protected string $bookingId){}
    public function viaQueue(){
        return 'default';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(config('services.mail.simulate_failure')){
            throw new RuntimeException("Simulated email failure");
        }else{
            Mail::raw("Hello {$this->user->name}, We regret to inform you that the event '{$this->eventTitle}' has been cancelled.", function ($message) {
                $message->to($this->user->email)
                    ->subject("Cancellation Notice: {$this->eventTitle}");
            });
        }
    }
    public function failed(Throwable $exception){
        Log::error('Send Cancellation Email job failed', [
            'event_title' => $this->eventTitle,
            'user_email' => $this->user->email,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
