<?php

namespace App\Services\SMSProvider;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class FakeSMSProvider implements SMSProviderInterface
{
    public function send(string $to, string $message): void
    {
        if(config('services.sms.simulate_failure')){
            throw new RuntimeException("Simulated email failure");
        }else{
            Log::info('=== [SMS SIMULATION] ===');
            Log::info("To: {$to}");
            Log::info("Message: {$message}");
        }
    }
}
