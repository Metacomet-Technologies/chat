<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Data\V1;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class CreateRoomRequest extends Data
{
    public function __construct(
        #[Required, StringType, Min(1), Max(100)]
        public string $name,
        
        #[StringType, Max(500)]
        public ?string $description,
        
        #[Required, StringType, Min(3), Max(50), Unique('rooms', 'slug')]
        public string $slug,
        
        public bool $is_public = true,
    ) {
    }
}