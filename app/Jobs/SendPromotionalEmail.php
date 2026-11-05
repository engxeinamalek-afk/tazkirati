<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
class SendPromotionalEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $email, private string $subject, private string $body)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw($this->body, function ($message) {
            $message->to($this->email)
                ->subject($this->subject);
        });
    }
}
