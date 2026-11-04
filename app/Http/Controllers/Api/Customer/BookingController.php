<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, BookingService $bookingService): JsonResponse
    {
        $customer = $request->user();
        $data = $request->validated();

        $booking = $bookingService->create(
            $customer,
            (int) $data['event_id'],
            $data['tickets']
        );

        return response()->json([
            'message' => 'تم إنشاء الحجز بنجاح.',
            'data' => $booking,
        ], 201);
    }
}
