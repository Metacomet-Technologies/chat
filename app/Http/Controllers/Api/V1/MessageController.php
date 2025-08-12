<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Messages\Actions\V1\GetMessagesAction;
use App\Domains\Messages\Actions\V1\SendMessageAction;
use App\Domains\Messages\Data\V1\MessageListResponse;
use App\Domains\Messages\Data\V1\MessageResponse;
use App\Domains\Messages\Data\V1\SendMessageRequest;
use App\Domains\Messages\Models\Room;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private readonly SendMessageAction $sendMessageAction,
        private readonly GetMessagesAction $getMessagesAction,
    ) {}

    public function index(Request $request, Room $room): MessageListResponse
    {
        return $this->getMessagesAction->execute([
            'room_id' => $room->id,
            'user_id' => $request->user()->id,
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 50),
        ]);
    }

    public function store(SendMessageRequest $data, Request $request, Room $room): MessageResponse
    {
        $validated = $data->toArray();

        return $this->sendMessageAction->execute([
            'room_id' => $room->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'text',
            'metadata' => $validated['metadata'] ?? null,
        ]);
    }
}
