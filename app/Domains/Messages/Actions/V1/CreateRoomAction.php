<?php

declare(strict_types=1);

namespace App\Domains\Messages\Actions\V1;

use App\Domains\Messages\Data\V1\RoomResponse;
use App\Domains\Messages\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateRoomAction
{
    /**
     * @param  array{name: string, type?: string, is_private?: bool, user_ids?: array<int>, creator_id?: int}  $data
     */
    public function execute(array $data): RoomResponse
    {
        return DB::transaction(function () use ($data) {
            $room = Room::create([
                'name' => $data['name'],
                'type' => $data['type'] ?? 'public',
                'is_private' => $data['is_private'] ?? false,
            ]);

            $userIds = $data['user_ids'] ?? [];

            if (! empty($userIds)) {
                $users = User::whereIn('id', $userIds)->get();

                foreach ($users as $user) {
                    $room->users()->attach($user->id, [
                        'joined_at' => now(),
                    ]);
                }
            }

            if (isset($data['creator_id'])) {
                $room->users()->syncWithoutDetaching([
                    $data['creator_id'] => ['joined_at' => now()],
                ]);
            }

            return RoomResponse::fromModel($room->fresh('users'));
        });
    }
}
