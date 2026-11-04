<?php

namespace App\Services\SMSProvider;

use Illuminate\Support\Facades\Log;

class FakeSMSProvider implements SMSProviderInterface
{
    public function send(string $to, string $message): void
    {
        Log::info('=== [SMS SIMULATION] ===');
        Log::info("To: {$to}");
        Log::info("Message: {$message}");
    }
}
