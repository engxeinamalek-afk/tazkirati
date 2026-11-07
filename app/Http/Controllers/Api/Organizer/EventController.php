<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Scopes\PublishedScope;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Traits\ApiResponse;
class EventController extends Controller
{
    use ApiResponse;
    public function store(StoreEventRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['organizer_id'] = auth()->id();

        $event = Event::create($validatedData);

        return $this->successResponse(new EventResource($event),"Event created successfully",201);
    }

    public function updateStatus(Event $event)
    {
        Gate::authorize('manage', $event);
        
        $event->update(['status' => EventStatus::PUBLISHED->value]);
        if ($event->wasChanged('status')) {
            return $this->successResponse($event, "Event status updated successfully");
        } else {
            return $this->successResponse(new EventResource($event), 'Event status is already published');
        }
    }

    public function cancel(Event $event, EventService $eventService): JsonResponse
    {
        Gate::authorize('manage', $event);

        $event = $eventService->cancel($event);

        return $this->successResponse(new EventResource($event), "Cancel event done successfully");
    }
    public function index(){
        $organizer= auth()->user();
        $events= $organizer->events()
                ->withoutGlobalScope(PublishedScope::class)
                ->select('id', 'organizer_id', 'title', 'status','venue_id')
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
