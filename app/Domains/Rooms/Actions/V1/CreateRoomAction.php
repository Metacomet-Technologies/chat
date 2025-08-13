<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Actions\V1;

use App\Domains\Rooms\Data\V1\RoomResponse;
use App\Domains\Rooms\Models\Room;
use App\Models\User;
use Illuminate\Support\Str;

class CreateRoomAction
{
    public function execute(array $data, User $creator): RoomResponse
    {
        $room = Room::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'is_public' => $data['is_public'] ?? true,
            'created_by_id' => $creator->id,
        ]);

        // Add creator as admin
        $room->addMember($creator, 'admin');

        return RoomResponse::fromModel($room, $creator->id);
    }
}