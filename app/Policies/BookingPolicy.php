<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;
class BookingPolicy
{
    public function access(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }
}
