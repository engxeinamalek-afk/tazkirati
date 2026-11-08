<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Event;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $totalTickets = fake()->numberBetween(1, 3);

        return [
            'user_id' => User::where('role', 'customer')->inRandomOrder()->first()?->id ?? User::factory()->customer(),
            'event_id' => Event::inRandomOrder()->first()?->id ?? Event::factory(),
            'total_tickets' => $totalTickets,
            'total_price' => $totalTickets * 25,
            'status' => fake()->randomElement(array_column(BookingStatus::cases(), 'value')),
        ];
    }
}
