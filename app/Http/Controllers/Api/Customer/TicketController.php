<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
class TicketController extends Controller
{
    public function getCount(Event $event){
        $count = Cache::remember("remaining_event_{$event->id}_tickets", now()->addMinute(1) , function () use($event){
            $event->loadSum('ticketTypes as total_seats', 'total_quantity');
            $event->loadCount('tickets as booked_seats');
            return $event->total_seats - $event->booked_seats;
        });
        return response()->json(['count'=> $count]);
    }
}
