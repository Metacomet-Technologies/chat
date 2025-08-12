<?php

declare(strict_types=1);

namespace App\Domains\Messages\Actions\V1;

use App\Domains\Messages\Data\V1\RoomResponse;
use App\Models\User;
use Spatie\LaravelData\DataCollection;

class GetRoomsAction
{
    /**
     * @param  array{user_id: int}  $data
     * @return DataCollection<int, RoomResponse>
     */
    public function execute(array $data): DataCollection
    {
        $user = User::findOrFail($data['user_id']);

        $rooms = $user->rooms()
            ->with(['users', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get();

        return RoomResponse::collect($rooms, DataCollection::class);
    }
}
