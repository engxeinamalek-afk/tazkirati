<?php
namespace App\Listeners;

use App\Events\EventCreated; 
use App\Models\Event;
use App\Models\User;
use App\Jobs\SendAdminNotificationJob;
use App\Events\EventCancelled;
use App\Notifications\EventCancelledNotification;
use App\Models\Scopes\PublishedScope;
use Illuminate\Support\Facades\Notification;

class SendCancelledNotification
{
    public function handle(EventCancelled $event)
    {
        $eventDetails = Event::withoutGlobalScope(PublishedScope::class)
            ->select('id', 'title')
            ->findOrFail($event->eventId);

        $admins = User::select('id')
            ->where('role', 'admin') 
            ->get();

        Notification::send($admins, new EventCancelledNotification($eventDetails->title));
    }
}
