<?php

declare(strict_types=1);

namespace App\Domains\Messages\Data\V1;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateRoomRequest extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,

        #[In(['public', 'private', 'direct'])]
        public string $type = 'public',

        public bool $isPrivate = false,

        /** @var array<int> */
        public array $userIds = [],
    ) {}
}
