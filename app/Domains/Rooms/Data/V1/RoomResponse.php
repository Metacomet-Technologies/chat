<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Data\V1;

use App\Domains\Rooms\Models\Room;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class RoomResponse extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $slug,
        public bool $is_public,
        public int $created_by_id,
        public string $creator_name,
        public int $member_count,
        public Carbon $created_at,
        public Carbon $updated_at,
        public Lazy|bool $is_member,
        public Lazy|string $user_role,
        public Lazy|Carbon $joined_at,
    ) {
    }

    public static function fromModel(Room $room, ?int $userId = null): self
    {
        $room->loadCount('members');
        $room->load('creator');
        
        $memberData = null;
        if ($userId) {
            $memberData = $room->members()
                ->where('user_id', $userId)
                ->first();
        }

        return new self(
            id: $room->id,
            name: $room->name,
            description: $room->description,
            slug: $room->slug,
            is_public: $room->is_public,
            created_by_id: $room->created_by_id,
            creator_name: $room->creator->name,
            member_count: $room->members_count,
            created_at: $room->created_at,
            updated_at: $room->updated_at,
            is_member: Lazy::when(
                fn () => $userId !== null,
                fn () => $memberData !== null
            ),
            user_role: Lazy::when(
                fn () => $memberData !== null,
                fn () => $memberData->pivot->role ?? 'member'
            ),
            joined_at: Lazy::when(
                fn () => $memberData !== null,
                fn () => $memberData->pivot->joined_at
            ),
        );
    }
}