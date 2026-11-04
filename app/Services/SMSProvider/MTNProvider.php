<?php

namespace App\Services\SMSProvider;

class MTNProvider implements SMSProviderInterface
{
    public function send(string $to, string $message): void
    {
        // كود البروفايدر الحقيقي
    }
}
