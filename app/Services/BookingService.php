<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\InsufficientTicketCapacityException;
use App\Jobs\ExpirePendingBooking;
use App\Models\Booking;
use App\Models\EventTicketType;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Events\BookingConfirmed;
use App\Exceptions\InvalidBookingStatusException;
class BookingService
{
    public function create(User $customer, int $eventId, array $tickets): Booking
    {
        // مصفوفة تحتوي النوع والكمية المطلوبة منه
        $requestedQuantities = [];
        foreach ($tickets as $ticket) {
            $requestedQuantities[$ticket['type']] = $ticket['quantity'];
        }

        return DB::transaction(function () use ($customer, $eventId, $tickets, $requestedQuantities) {
            // الحصول على انواع التذاكر المطلوبة 
            $ticketTypeRecords = EventTicketType::query()
                ->forEvent($eventId)
                ->forTypes(array_keys($requestedQuantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $ticketTypes = [];
            $ticketTypeIds = [];
            foreach ($ticketTypeRecords as $ticketType) {
                // مصفوفة بالشكل 'type' => EventTicketType
                $ticketTypes[$ticketType->type] = $ticketType;
                // مصفوفة بالشكل [id1, id2, ...] 
                $ticketTypeIds[] = $ticketType->id;
            }

            // جلب الكميات المباعة لكل نوع تذكرة من التذاكر المطلوبة
            $soldQuantityRecords = Ticket::query()
                ->forTicketTypes($ticketTypeIds)
                ->whereHas('booking', fn ($query) => $query->reservingSeats())
                ->selectRaw('event_ticket_type_id, COUNT(*) as quantity')
                ->groupBy('event_ticket_type_id')
                ->get();

            $soldQuantities = [];
            foreach ($soldQuantityRecords as $soldQuantity) {
                $soldQuantities[$soldQuantity->event_ticket_type_id] = (int) $soldQuantity->quantity;
            }

            $this->ensureCapacity($ticketTypes, $requestedQuantities, $soldQuantities);

            $totalTickets = array_sum($requestedQuantities);
            // حساب السعر الكلي
            $totalPrice = $this->calculateTotalPrice($ticketTypes, $requestedQuantities);

            // انشاء الحجز
            $booking = Booking::create([
                'user_id' => $customer->id,
                'event_id' => $eventId,
                'total_tickets' => $totalTickets,
                'total_price' => $totalPrice,
                'status' => BookingStatus::PENDING->value,
            ]);

            // انشاء التذاكر
            $ticketRows = [];
            foreach ($tickets as $ticket) {
                for ($quantity = 0; $quantity < (int) $ticket['quantity']; $quantity++) {
                    $ticketRows[] = [
                        'event_ticket_type_id' => $ticketTypes[$ticket['type']]->id,
                    ];
                }
            }
            $booking->tickets()->createMany($ticketRows);

            // اطلاق job متأخر يتحقق اذا تم الدفع او لا
            ExpirePendingBooking::dispatch($booking->id)
                ->delay(now()->addMinutes(10));

            return $booking->load('tickets');
        });
    }

    private function ensureCapacity(
        array $ticketTypes,
        array $requestedQuantities,
        array $soldQuantities
    ): void {
        foreach ($ticketTypes as $ticketType) {
            $requested = $requestedQuantities[$ticketType->type] ?? 0;
            $sold = $soldQuantities[$ticketType->id] ?? 0;

            if ($sold + $requested > $ticketType->total_quantity) {
                $message = "لا توجد مقاعد كافية لنوع التذكرة: {$ticketType->type}.";

                throw new InsufficientTicketCapacityException($message, [
                    'tickets' => [$message],
                ]);
            }
        }
    }

    private function calculateTotalPrice(array $ticketTypes, array $requestedQuantities): int
    {
        $totalPrice = 0;

        foreach ($ticketTypes as $ticketType) {
            $quantity = $requestedQuantities[$ticketType->type] ?? 0;
            $totalPrice += $ticketType->price * $quantity;
        }

        return $totalPrice;
    }



    public function paid(Booking $booking){
        $this->checkStatus($booking);
        return DB::transaction(function () use ($booking) {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            
            // محاكاة للدفع (فقط تغيير الحالة)
            $booking->update([
                'status' => BookingStatus::PAID->value,
            ]);

            BookingConfirmed::dispatch($booking->id);

            return $booking->load('tickets');
        });
    }
    private function checkStatus(Booking $booking){
        if($booking->status !== BookingStatus::PENDING){
            throw new InvalidBookingStatusException('You cannot pay for this booking in its current status.');   
        }
    }
}
