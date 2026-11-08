<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventTicketType;
use Illuminate\Database\Seeder;
use App\Enums\TicketType;
class EventTicketTypeSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();

        foreach ($events as $event) {
            EventTicketType::firstOrCreate(
                ['event_id' => $event->id, 'type' => TicketType::NORMAL->value],
                [
                    'price' => fake()->numberBetween(10, 150),
                    'total_quantity' => fake()->randomElement([50, 100, 200, 500]),
                ]
            );

            EventTicketType::firstOrCreate(
                ['event_id' => $event->id, 'type' => TicketType::VIP->value],
                [
                    'price' => fake()->numberBetween(10, 150),
                    'total_quantity' => fake()->randomElement([50, 100, 200, 500]),
                ]
            );

        }
    }
}
