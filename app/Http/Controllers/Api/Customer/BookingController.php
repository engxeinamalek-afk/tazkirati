<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

    public function payForBooking(Booking $booking, BookingService $bookingService): JsonResponse
    {
        Gate::authorize('access', $booking);

        $paidBooking = $bookingService->paid($booking);

        return response()->json([
            'success' => true,
            'message' => 'تمت عملية الدفع ومحاكاتها بنجاح، وتم تأكيد الحجز.',
            'data' => $paidBooking,
        ], 200);
    }
}
