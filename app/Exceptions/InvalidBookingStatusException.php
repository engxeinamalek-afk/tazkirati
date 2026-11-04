<?php

namespace App\Exceptions;

use Exception;

class InvalidBookingStatusException extends BaseApiException
{
    protected int $statusCode = 409;
}
