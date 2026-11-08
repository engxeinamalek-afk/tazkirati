<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Venue;
use App\Enums\EventStatus; 
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'venue_id' => Venue::inRandomOrder()->first()?->id ?? Venue::factory(), 
            'organizer_id' => User::where('role', 'organizer')->inRandomOrder()->first()?->id ?? User::factory()->organizer(),
            'title' => $this->faker->sentence(3),
            
            'status' => fake()->randomElement(array_column(EventStatus::cases(), 'value')),
        ];
    }
}
