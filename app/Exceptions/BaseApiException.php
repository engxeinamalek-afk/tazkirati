<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;

abstract class BaseApiException extends Exception
{
    use ApiResponse;
    /**
     * كود حالة HTTP الافتراضي
     */
    protected int $statusCode = 400;

    /**
     * مصفوفة تفاصيل الأخطاء
     */
    protected array $errors = [];

    public function __construct(string $message = "", array $errors = [], ?int $statusCode = null)
    {
        $this->errors = $errors;

        if ($statusCode !== null) {
            $this->statusCode = $statusCode;
        }

        parent::__construct($message, $this->statusCode);
    }

    /**
     * جلب مصفوفة الأخطاء
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * جلب كود الحالة
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function render($request)
    {
        return $this->errorResponse($this->getMessage(), $this->getErrors(), $this->statusCode);
    }
}
