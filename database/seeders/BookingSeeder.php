<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $events = Event::has('ticketTypes')->whereNot('id', 1)->get();

        foreach ($customers as $customer) {
            $maxCount = min($events->count(), 2);
            if ($maxCount > 0) {
                $randomEvents = $events->random(fake()->numberBetween(1, $maxCount));
                    foreach ($randomEvents as $event) {
                        $ticketType = $event->ticketTypes()->inRandomOrder()->first();
                        
                        if (!$ticketType) {
                            continue;
                        }

                        $ticketsCount = fake()->numberBetween(1, 3);
                        $totalPrice = $ticketsCount * $ticketType->price; 

                        $booking = Booking::factory()->create([
                            'user_id' => $customer->id,
                            'event_id' => $event->id,
                            'total_tickets' => $ticketsCount,
                            'total_price' => $totalPrice,
                        ]);

                        for ($i = 0; $i < $ticketsCount; $i++) {
                            Ticket::create([
                                'booking_id' => $booking->id,
                                'event_ticket_type_id' => $ticketType->id,
                                'ticket_code' => 'TCK-' . Str::upper((string) Str::uuid()),
                            ]);
                        }
                    }
                }
        }
    }
}
