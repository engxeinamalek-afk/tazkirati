<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(){
        $events= Event::select('id', 'title', 'venue_id')
                    ->with('venue:id,name')
                    ->paginate(10);
        return $events;
    }
}
