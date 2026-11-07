<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;
trait ApiResponse
{
    public function successResponse($data= null, string $message = 'Operation successful.', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $statusCode);
    }

    public function errorResponse(string $message, $errors = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'   => $errors,
        ], $statusCode);
    }
}
