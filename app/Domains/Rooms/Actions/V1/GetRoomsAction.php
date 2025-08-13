<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Actions\V1;

use App\Domains\Rooms\Data\V1\RoomResponse;
use App\Domains\Rooms\Models\Room;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\PaginatedDataCollection;

class GetRoomsAction
{
    public function execute(?User $user = null, bool $onlyMemberRooms = false, int $perPage = 15): PaginatedDataCollection
    {
        $query = Room::query()->withCount('members');

        if ($onlyMemberRooms && $user) {
            // Get only rooms where user is a member
            $query->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            // Get public rooms and rooms where user is a member
            $query->where(function ($q) use ($user) {
                $q->where('is_private', false);
                
                if ($user) {
                    $q->orWhereHas('members', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
                }
            });
        }

        $rooms = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return RoomResponse::collect($rooms, PaginatedDataCollection::class)
            ->through(fn ($room) => RoomResponse::fromModel($room, $user?->id));
    }
}