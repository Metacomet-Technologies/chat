<?php

namespace App\Broadcasting;

use App\Domains\Rooms\Models\Room;
use App\Models\User;

class RoomChannel
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Room $room): array|bool
    {
        if ($room->isMember($user)) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        }
        
        return false;
    }
}