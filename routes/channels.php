<?php

use App\Domains\Messages\Models\Room;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('shared', function ($user) {
    return true;
});

Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    $room = Room::find($roomId);

    if (! $room) {
        return false;
    }

    return $room->hasUser($user);
});
