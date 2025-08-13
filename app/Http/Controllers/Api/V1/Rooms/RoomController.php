<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Rooms;

use App\Domains\Rooms\Actions\V1\CreateRoomAction;
use App\Domains\Rooms\Actions\V1\GetRoomMembersAction;
use App\Domains\Rooms\Actions\V1\GetRoomsAction;
use App\Domains\Rooms\Actions\V1\JoinRoomAction;
use App\Domains\Rooms\Actions\V1\LeaveRoomAction;
use App\Domains\Rooms\Data\V1\CreateRoomRequest;
use App\Domains\Rooms\Data\V1\JoinRoomRequest;
use App\Domains\Rooms\Data\V1\RoomMemberResponse;
use App\Domains\Rooms\Data\V1\RoomResponse;
use App\Domains\Rooms\Models\Room;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class RoomController extends Controller
{

    public function index(Request $request, GetRoomsAction $action): PaginatedDataCollection
    {
        $onlyMemberRooms = $request->boolean('member_rooms', false);
        
        return $action->execute(
            user: $request->user(),
            onlyMemberRooms: $onlyMemberRooms,
            perPage: $request->integer('per_page', 15)
        );
    }

    public function store(CreateRoomRequest $request, CreateRoomAction $action): RoomResponse
    {
        return $action->execute(
            data: $request->toArray(),
            creator: auth()->user()
        );
    }

    public function show(Room $room): RoomResponse
    {
        Gate::authorize('view', $room);
        
        return RoomResponse::fromModel($room, auth()->id());
    }

    public function join(JoinRoomRequest $request, JoinRoomAction $action): RoomResponse
    {
        $room = Room::findOrFail($request->room_id);
        Gate::authorize('join', $room);
        
        return $action->execute($request->room_id, auth()->user());
    }

    public function leave(Room $room, LeaveRoomAction $action): JsonResponse
    {
        Gate::authorize('leave', $room);
        
        $action->execute($room->id, request()->user());
        
        return response()->json(['message' => 'Successfully left the room']);
    }

    public function members(Room $room, GetRoomMembersAction $action): DataCollection
    {
        Gate::authorize('view', $room);
        
        return $action->execute($room->id);
    }

    public function destroy(Room $room): JsonResponse
    {
        Gate::authorize('delete', $room);
        
        $room->delete();
        
        return response()->json(['message' => 'Room deleted successfully']);
    }
}