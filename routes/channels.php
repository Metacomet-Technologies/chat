<?php

use App\Broadcasting\RoomChannel;
use App\Domains\Rooms\Models\Room;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('shared', function ($user) {
    return true;
});

Broadcast::channel('room.{room}', RoomChannel::class . ':join');
