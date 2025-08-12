<?php

declare(strict_types=1);

namespace App\Domains\Messages\Data\V1;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class MessageListResponse extends Data
{
    /**
     * @param  DataCollection<int, MessageResponse>  $messages
     */
    public function __construct(
        public DataCollection $messages,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
        public ?string $nextPageUrl,
        public ?string $previousPageUrl,
    ) {}

    /**
     * @param  LengthAwarePaginator<int, \App\Domains\Messages\Models\Message>  $paginator
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): self
    {
        return new self(
            messages: MessageResponse::collect($paginator->items(), DataCollection::class),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            nextPageUrl: $paginator->nextPageUrl(),
            previousPageUrl: $paginator->previousPageUrl(),
        );
    }
}
