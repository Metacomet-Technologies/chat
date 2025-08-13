<?php

namespace App\Policies;

use App\Domains\Rooms\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function view(User $user, Room $room): bool
    {
        // User can view if room is public or they're a member
        return !$room->is_private || $room->isMember($user);
    }

    public function join(User $user, Room $room): bool
    {
        // User can join if room is public and they're not already a member
        return !$room->is_private && !$room->isMember($user);
    }

    public function leave(User $user, Room $room): bool
    {
        // User can leave if they're a member and not the creator
        return $room->isMember($user) && $room->created_by_id !== $user->id;
    }

    public function update(User $user, Room $room): bool
    {
        // Only admins can update the room
        return $room->isAdmin($user);
    }

    public function delete(User $user, Room $room): bool
    {
        // Only the creator can delete the room
        return $room->created_by_id === $user->id;
    }

    public function manageMember(User $user, Room $room): bool
    {
        // Only admins can manage members
        return $room->isAdmin($user);
    }
}