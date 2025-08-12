<?php

declare(strict_types=1);

namespace App\Domains\Messages\Data\V1;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class SendMessageRequest extends Data
{
    public function __construct(
        #[Required, StringType, Max(5000)]
        public string $content,

        #[StringType]
        public string $type = 'text',

        /** @var array<string, mixed>|null */
        public ?array $metadata = null,
    ) {}
}
