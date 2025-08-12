<?php

declare(strict_types=1);

namespace App\Domains\Messages\Actions\V1;

use App\Domains\Messages\Data\V1\MessageListResponse;
use App\Domains\Messages\Models\Room;
use App\Models\User;
use Exception;

class GetMessagesAction
{
    /**
     * @param  array{room_id: int, user_id: int, per_page?: int, page?: int}  $data
     */
    public function execute(array $data): MessageListResponse
    {
        $room = Room::findOrFail($data['room_id']);
        $user = User::findOrFail($data['user_id']);

        if (! $room->hasUser($user)) {
            throw new Exception('User is not a member of this room');
        }

        $perPage = $data['per_page'] ?? 50;
        $page = $data['page'] ?? 1;

        $messages = $room->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $room->users()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        return MessageListResponse::fromPaginator($messages);
    }
}
