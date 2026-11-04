<?php

namespace App\Services\SMSProvider;

interface SMSProviderInterface
{
    public function send(string $to, string $message): void;
}
