<?php

declare(strict_types=1);

namespace App\Domains\Messages\Actions\V1;

use App\Domains\Messages\Data\V1\RoomResponse;
use App\Domains\Messages\Models\Room;
use App\Models\User;
use Exception;

class JoinRoomAction
{
    /**
     * @param  array{room_id: int, user_id: int}  $data
     */
    public function execute(array $data): RoomResponse
    {
        $room = Room::findOrFail($data['room_id']);
        $user = User::findOrFail($data['user_id']);

        if ($room->hasUser($user)) {
            throw new Exception('User is already a member of this room');
        }

        if ($room->is_private) {
            throw new Exception('Cannot join a private room without invitation');
        }

        $room->users()->attach($user->id, [
            'joined_at' => now(),
        ]);

        return RoomResponse::fromModel($room->fresh('users'));
    }
}
