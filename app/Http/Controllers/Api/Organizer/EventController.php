<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\Scopes\PublishedScope;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function store(StoreEventRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['organizer_id'] = auth()->id();

        $event = Event::create($validatedData);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event,
        ], 201);
    }

    public function updateStatus(Event $event)
    {
        Gate::authorize('manage', $event);
        
        $event->update(['status' => EventStatus::PUBLISHED->value]);
        if ($event->wasChanged('status')) {
            return response()->json([
                'message' => 'Event status updated successfully',
                'event' => $event,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Event status is already published',
                'event' => $event,
            ], 200);
        }
    }

    public function cancel(Event $event, EventService $eventService): JsonResponse
    {
        Gate::authorize('manage', $event);

        $event = $eventService->cancel($event);

        return response()->json([
            'message' => 'تم إلغاء الفعالية وإشعار أصحاب الحجوزات.',
            'event' => $event,
        ]);
    }
    public function index(){
        $organizer= auth()->user();
        $events= $organizer->events()
                ->withoutGlobalScope(PublishedScope::class)
                ->select('id', 'organizer_id', 'title', 'status','venue_id')
                ->with('venue:id,name')
                ->paginate(10);
        return $events;
    }
}
