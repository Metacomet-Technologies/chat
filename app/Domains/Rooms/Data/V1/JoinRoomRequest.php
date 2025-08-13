<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Data\V1;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class JoinRoomRequest extends Data
{
    public function __construct(
        #[Required, Exists('rooms', 'id')]
        public int $room_id,
    ) {
    }
}