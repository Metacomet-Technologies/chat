<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Actions\V1;

use App\Domains\Rooms\Data\V1\RoomMemberResponse;
use App\Domains\Rooms\Models\Room;
use Spatie\LaravelData\DataCollection;

class GetRoomMembersAction
{
    public function execute(int $roomId): DataCollection
    {
        $room = Room::with('members')->findOrFail($roomId);
        
        $members = $room->members->map(function ($user) {
            return RoomMemberResponse::fromUser(
                $user,
                $user->pivot->role,
                \Illuminate\Support\Carbon::parse($user->pivot->joined_at)
            );
        });

        return RoomMemberResponse::collect($members, DataCollection::class);
    }
}