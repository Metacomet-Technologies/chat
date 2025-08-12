<?php

declare(strict_types=1);

namespace App\Domains\Messages\Data\V1;

use App\Domains\Messages\Models\Message;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class MessageResponse extends Data
{
    public function __construct(
        public int $id,
        public int $roomId,
        public int $userId,
        public string $content,
        public string $type,
        /** @var array<string, mixed>|null */
        public ?array $metadata,
        public Lazy|UserData $user,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}

    public static function fromModel(Message $message): self
    {
        return new self(
            id: $message->id,
            roomId: $message->room_id,
            userId: $message->user_id,
            content: $message->content,
            type: $message->type,
            metadata: $message->metadata,
            user: Lazy::create(fn () => UserData::fromModel($message->user)),
            createdAt: $message->created_at,
            updatedAt: $message->updated_at,
        );
    }
}
