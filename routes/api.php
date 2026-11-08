<?php

use App\Http\Controllers\Api\Customer\BookingController;
use App\Http\Controllers\Api\Customer\EventController as CustomerEventController;
use App\Http\Controllers\Api\Customer\TicketController;
use App\Http\Controllers\Api\Organizer\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organizer\EventNotificationController;
use App\Http\Controllers\Api\Organizer\ImageController;

Route::middleware(['auth:sanctum','role:organizer'])->prefix('organizer')->group(function () {

    Route::patch('/events/{event}/cancel', [EventController::class, 'cancel']);
    Route::patch('/events/{event}/status', [EventController::class, 'updateStatus']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events', [EventController::class, 'index']);

    Route::post('/events/{event}/emails', [EventNotificationController::class, 'sendEmail']);

    Route::post('/images/{imageable_type}/{imageable}', [ImageController::class, 'store']);
});

Route::middleware(['auth:sanctum','role:customer'])->prefix('customer')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/bookings/{booking}/pay', [BookingController::class, 'payForBooking']);
    Route::get('/events', [CustomerEventController::class, 'index']);
    Route::get('/events/{event}/seats_count', [TicketController::class, 'getCount']);
});
