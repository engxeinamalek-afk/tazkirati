<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
class SendCancellationEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user, protected string $eventTitle, protected string $bookingId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw("Hello {$this->user->name}, We regret to inform you that the event '{$this->eventTitle}' has been cancelled.", function ($message) {
            $message->to($this->user->email)
                ->subject("Cancellation Notice: {$this->eventTitle}");
        });
    }
}
