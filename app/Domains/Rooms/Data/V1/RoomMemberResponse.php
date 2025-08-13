<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Data\V1;

use App\Models\User;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class RoomMemberResponse extends Data
{
    public function __construct(
        public int $user_id,
        public string $name,
        public string $email,
        public string $role,
        public Carbon $joined_at,
        public bool $is_online,
    ) {
    }

    public static function fromUser(User $user, string $role, Carbon $joinedAt): self
    {
        return new self(
            user_id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $role,
            joined_at: $joinedAt,
            is_online: false, // This will be updated via WebSocket presence channel
        );
    }
}