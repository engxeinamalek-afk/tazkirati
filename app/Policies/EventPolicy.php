<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
class EventPolicy
{
    public function manage(User $user, Event $event)
    {
        return $user->id === $event->organizer_id;
    }
}
