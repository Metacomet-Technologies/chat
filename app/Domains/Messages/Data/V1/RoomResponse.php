<?php

declare(strict_types=1);

namespace App\Domains\Messages\Data\V1;

use App\Domains\Messages\Models\Room;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;

class RoomResponse extends Data
{
    /**
     * @param  Lazy|DataCollection<int, UserData>  $users
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
        public bool $isPrivate,
        public Lazy|DataCollection $users,
        public ?int $unreadCount,
        public ?Carbon $lastMessageAt,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}

    public static function fromModel(Room $room): self
    {
        $lastMessage = $room->messages()->latest()->first();

        return new self(
            id: $room->id,
            name: $room->name,
            type: $room->type,
            isPrivate: $room->is_private,
            users: Lazy::create(fn () => UserData::collect($room->users, DataCollection::class)),
            unreadCount: null,
            lastMessageAt: $lastMessage?->created_at,
            createdAt: $room->created_at,
            updatedAt: $room->updated_at,
        );
    }
}
