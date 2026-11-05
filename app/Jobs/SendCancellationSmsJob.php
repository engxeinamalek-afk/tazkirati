<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\SMSProvider\SMSProviderInterface;
class SendCancellationSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $phone,
                                protected string $userName,
                                protected string $eventTitle)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SMSProviderInterface $smsProvider): void
    {
        $smsProvider->send($this->phone, "Hello {$this->userName}, We regret to inform you that the event '{$this->eventTitle}' has been cancelled.");
    }
}
