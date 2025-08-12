<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Messages\Actions\V1\CreateRoomAction;
use App\Domains\Messages\Actions\V1\GetRoomsAction;
use App\Domains\Messages\Actions\V1\JoinRoomAction;
use App\Domains\Messages\Data\V1\CreateRoomRequest;
use App\Domains\Messages\Data\V1\RoomResponse;
use App\Domains\Messages\Models\Room;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\LaravelData\DataCollection;

class RoomController extends Controller
{
    public function __construct(
        private readonly CreateRoomAction $createRoomAction,
        private readonly GetRoomsAction $getRoomsAction,
        private readonly JoinRoomAction $joinRoomAction,
    ) {}

    /**
     * @return DataCollection<int, RoomResponse>
     */
    public function index(Request $request): DataCollection
    {
        return $this->getRoomsAction->execute([
            'user_id' => $request->user()->id,
        ]);
    }

    public function store(CreateRoomRequest $data, Request $request): RoomResponse
    {
        $validated = $data->toArray();

        return $this->createRoomAction->execute([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? 'public',
            'is_private' => $validated['isPrivate'] ?? false,
            'user_ids' => $validated['userIds'] ?? [],
            'creator_id' => $request->user()->id,
        ]);
    }

    public function show(Request $request, Room $room): RoomResponse
    {
        if (! $room->hasUser($request->user())) {
            abort(403, 'You are not a member of this room');
        }

        return RoomResponse::fromModel($room->load('users'));
    }

    public function join(Request $request, Room $room): RoomResponse
    {
        return $this->joinRoomAction->execute([
            'room_id' => $room->id,
            'user_id' => $request->user()->id,
        ]);
    }
}
