<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Venue;
use App\Models\User;
use App\Enums\EventStatus;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::factory()->count(100)->create();
    }
}
