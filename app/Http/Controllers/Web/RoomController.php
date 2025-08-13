<?php

namespace App\Http\Controllers\Web;

use App\Domains\Rooms\Actions\V1\GetRoomMembersAction;
use App\Domains\Rooms\Actions\V1\GetRoomsAction;
use App\Domains\Rooms\Models\Room;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{

    public function index(Request $request, GetRoomsAction $action): Response
    {
        $rooms = $action->execute(
            user: $request->user(),
            onlyMemberRooms: false,
            perPage: 20
        );

        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
        ]);
    }

    public function show(Room $room, GetRoomMembersAction $membersAction): Response
    {
        Gate::authorize('view', $room);
        
        $members = $membersAction->execute($room->id);

        return Inertia::render('Rooms/Show', [
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'slug' => $room->slug,
                'is_public' => $room->is_public,
                'member_count' => $room->members()->count(),
                'user_role' => $room->members()
                    ->where('user_id', auth()->id())
                    ->first()
                    ?->pivot->role ?? 'member',
            ],
            'members' => $members,
        ]);
    }
}
