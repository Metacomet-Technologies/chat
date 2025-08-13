<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Actions\V1;

use App\Domains\Rooms\Data\V1\RoomResponse;
use App\Domains\Rooms\Models\Room;
use App\Models\User;

class JoinRoomAction
{
    public function execute(int $roomId, User $user): RoomResponse
    {
        $room = Room::findOrFail($roomId);
        
        if (!$room->isMember($user)) {
            $room->addMember($user);
        }

        return RoomResponse::fromModel($room, $user->id);
    }
}