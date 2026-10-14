<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view all events') || $user->can('create events');
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->can('view all events')) {
            return true;
        }

        return $user->can('view registrations') && $event->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create events');
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->can('view all events')) {
            return $user->can('update events');
        }

        return $user->can('update events') && $event->created_by === $user->id;
    }

    public function delete(User $user, Event $event): bool
    {
        if ($user->can('view all events')) {
            return $user->can('delete events');
        }

        return $user->can('delete events') && $event->created_by === $user->id;
    }
}
