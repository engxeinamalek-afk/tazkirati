<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Events\EventCancelled;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InvalidEventStatusException;
class EventService
{
    public function cancel(Event $event): Event
    {
        $this->checkStatus($event);
        return DB::transaction(function () use ($event): Event {
            $event = Event::query()->lockForUpdate()->findOrFail($event->id);

            $event->update(['status' => EventStatus::CANCELLED->value]);

            EventCancelled::dispatch($event->id);
            
            return $event->refresh();
        });
    }
    private function checkStatus(Event $event){
        if ($event->status === EventStatus::CANCELLED->value) {
            $message = 'Event is already cancelled.';
            throw new InvalidEventStatusException($message,[
                'event' => $message
            ]);
        }
    }
}
