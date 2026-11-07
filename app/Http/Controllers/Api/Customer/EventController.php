<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponse;
    public function index(){
        $events= Event::select('id', 'title', 'venue_id', 'organizer_id')
                    ->with('venue:id,name')
                    ->paginate(10);
        return $this->successResponse(data: [
            'events' => EventResource::collection($events),
            'links' => [
                        'current_page' => $events->currentPage(),
                        'next_page'    => $events->nextPageUrl(),
                        'prev_page'    => $events->previousPageUrl(),
                        'total_pages'  => $events->lastPage(),
                        'total_properties' => $events->total()
            ]
        ], message:"Events retrieved successfully!");
    }
}
