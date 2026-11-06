<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Illuminate\Support\Facades\Log;
use Throwable;
// اهمية قليلة
class SendPromotionalEmail implements ShouldQueue
{
    use Queueable;
    public $tries=2;
    public $timeout=30;
    public function backoff():array{return [30];}
    /**
     * Create a new job instance.
     */
    public function __construct(private string $email, private string $subject, private string $body){}
    public function viaQueue(){
        return 'emails';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(config('services.mail.simulate_failure')){
            throw new RuntimeException("Simulated email failure");
        }else{
            Mail::raw($this->body, function ($message) {
                $message->to($this->email)
                    ->subject($this->subject);
            });
        }
    }
    public function failed(Throwable $exception){
        Log::error('Send Promitional Email job failed', [
            'user_email' => $this->email,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
