<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\SMSProvider\SMSProviderInterface;
use Illuminate\Support\Facades\Log;
use Throwable;
// اهمية متوسطة
class SendCancellationSmsJob implements ShouldQueue
{
    use Queueable;
    public $tries=4;
    public $timeout=30;
    public function backoff():array{return [30,60,120];}
    /**
     * Create a new job instance.
     */
    public function __construct(protected string $phone,
                                protected string $userName,
                                protected string $eventTitle){}
    public function viaQueue(){
        return 'default';
    }
    /**
     * Execute the job.
     */
    public function handle(SMSProviderInterface $smsProvider): void
    {
        $smsProvider->send($this->phone, "Hello {$this->userName}, We regret to inform you that the event '{$this->eventTitle}' has been cancelled.");
    }
    public function failed(Throwable $exception){
        Log::error('Send Cancellation SMS job failed', [
            'user_phone' => $this->phone,
            'event_title' => $this->eventTitle,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
