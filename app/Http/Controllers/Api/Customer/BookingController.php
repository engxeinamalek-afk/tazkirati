<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class BookingController extends Controller
{
    use ApiResponse;
    public function store(StoreBookingRequest $request, BookingService $bookingService): JsonResponse
    {
        $customer = $request->user();
        $data = $request->validated();

        $booking = $bookingService->create(
            $customer,
            (int) $data['event_id'],
            $data['tickets']
        );

        return $this->successResponse(new BookingResource($booking), "Booking stored successfully!",201);
    }

    public function payForBooking(Booking $booking, BookingService $bookingService): JsonResponse
    {
        Gate::authorize('access', $booking);

        $paidBooking = $bookingService->paid($booking);

        return $this->successResponse(new BookingResource($paidBooking), "Paid done successfully!");
    }
}
