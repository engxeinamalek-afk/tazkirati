<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use App\Enums\TicketType;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com', 'role' => 'admin']);
        $organizerA = User::factory()->create(['email' => 'organizer_a@test.com', 'role' => 'organizer']);
        $organizerB = User::factory()->create(['email' => 'organizer_b@test.com', 'role' => 'organizer']);
        $customer = User::factory()->create(['email' => 'customer@test.com', 'role' => 'customer']);

        // ايفينت خاص بالمنظم الاول
        $eventActive = Event::factory()->create([
            'id' => 1, 
            'organizer_id' => $organizerA->id, 
            'status' => EventStatus::PUBLISHED->value, 
            'title' => 'Active Event A'
        ]);

        // تذاكر نوع عادي 
        $ticketTypeNormal = EventTicketType::create([
            'id' => 1,
            'event_id' => $eventActive->id,
            'type' => TicketType::NORMAL->value,
            'price' => 50,
            'total_quantity' => 50 
        ]);
        // تذاكر نوع vip
        $ticketTypeVIP = EventTicketType::create([
            'event_id' => $eventActive->id,
            'type' => TicketType::VIP->value,
            'price' => 150,
            'total_quantity' => 5
        ]);

        // ايفينت مُلغى
        $eventCancelled = Event::factory()->create([
            'id' => 2,
            'organizer_id' => $organizerA->id,
            'status' => EventStatus::CANCELLED->value,
            'title' => 'Cancelled Event'
        ]);


        // توليد توكنز للمنظمين
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id'   => $organizerA->id,
            'name'           => 'PostmanToken',
            'token'          => hash('sha256', 'organizer_a_token_secret'), 
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id'   => $organizerB->id,
            'name'           => 'PostmanToken',
            'token'          => hash('sha256', 'organizer_b_token_secret'), 
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // توكن للادمن
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id'   => $admin->id,
            'name'           => 'PostmanToken',
            'token'          => hash('sha256', 'admin_token_secret'), 
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        // توكن للزبون
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id'   => $customer->id,
            'name'           => 'PostmanToken',
            'token'          => hash('sha256', 'customer_token_secret'), 
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);





        $hugeEvent = Event::factory()->create([
            'id' => 3, 
            'organizer_id' => $organizerA->id,
            'title' => 'Huge Tech Conference 2026',
            'status' => EventStatus::PUBLISHED->value
        ]);

        $normalTickets = EventTicketType::create([
            'id' => 3,
            'event_id' => $hugeEvent->id,
            'type' => 'Normal',
            'price' => 50,
            'total_quantity' => 2000
        ]);

        $vipTickets = EventTicketType::create([
            'id' => 4,
            'event_id' => $hugeEvent->id,
            'type' => 'VIP',
            'price' => 200,
            'total_quantity' => 1000
        ]);

        $this->command->info('Starting to seed 2000 bookings... Please wait.');
        
        $totalBookingsCount = 2000;
        
        DB::transaction(function () use ($customer, $normalTickets, $vipTickets, $totalBookingsCount) {
            
            for ($i = 1; $i <= $totalBookingsCount; $i++) {
                $ticketType = ($i % 2 == 0) ? $normalTickets : $vipTickets;

                // إنشاء الحجز
                $booking = Booking::create([
                    'user_id' => $customer->id,
                    'event_id' => $ticketType->event_id,
                    'total_tickets' =>1,
                    'total_price' => $ticketType->price * 1,
                    'status' => BookingStatus::PAID->value,
                    'created_at' => now()->subMinutes($i),
                ]);

                // إنشاء التذكرة التابعة للحجز
                // Ticket::create([
                //     'booking_id' => $booking->id,
                //     'ticket_type_id' => $ticketType->id,
                //     'ticket_code' => 'TICK-' . strtoupper(Str::random(8)) . '-' . $booking->id,
                //     'price' => $ticketType->price
                // ]);
            }
        });

        $this->command->info('Successfully seeded 2000 bookings and tickets!');
    }

}
