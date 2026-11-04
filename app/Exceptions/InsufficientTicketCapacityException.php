<?php

namespace App\Exceptions;

class InsufficientTicketCapacityException extends BaseApiException
{
    protected int $statusCode = 422;
}
