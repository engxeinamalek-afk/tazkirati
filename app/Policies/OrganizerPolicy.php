<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizerPolicy
{
    public function manage(User $user, User $organizer)
    {
        return $user->id === $organizer->id;
    }
}
