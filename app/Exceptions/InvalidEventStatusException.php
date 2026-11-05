<?php

namespace App\Exceptions;

use Exception;

class InvalidEventStatusException extends BaseApiException
{
    protected int $statusCode = 409;
}
