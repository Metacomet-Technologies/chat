<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Actions\V1;

use App\Domains\Rooms\Models\Room;
use App\Models\User;

class LeaveRoomAction
{
    public function execute(int $roomId, User $user): bool
    {
        $room = Room::findOrFail($roomId);
        
        // Prevent creator from leaving (they must delete the room instead)
        if ($room->created_by_id === $user->id) {
            throw new \Exception('Room creator cannot leave the room. Please delete the room instead.');
        }
        
        if ($room->isMember($user)) {
            $room->removeMember($user);
            return true;
        }

        return false;
    }
}