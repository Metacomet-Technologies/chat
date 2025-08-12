<?php

declare(strict_types=1);

namespace App\Domains\Messages\Actions\V1;

use App\Domains\Messages\Data\V1\MessageResponse;
use App\Domains\Messages\Models\Message;
use App\Domains\Messages\Models\Room;
use App\Events\MessageSent;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class SendMessageAction
{
    /**
     * @param  array{room_id: int, user_id: int, content: string, type?: string, metadata?: array<string, mixed>|null}  $data
     */
    public function execute(array $data): MessageResponse
    {
        return DB::transaction(function () use ($data) {
            $room = Room::findOrFail($data['room_id']);
            $user = User::findOrFail($data['user_id']);

            if (! $room->hasUser($user)) {
                throw new Exception('User is not a member of this room');
            }

            $message = Message::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'content' => $data['content'],
                'type' => $data['type'] ?? 'text',
                'metadata' => $data['metadata'] ?? null,
            ]);

            $room->users()->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

            // Load the user relationship before broadcasting and returning
            $message->load('user');

            broadcast(new MessageSent($message));

            return MessageResponse::fromModel($message)->include('user');
        });
    }
}
