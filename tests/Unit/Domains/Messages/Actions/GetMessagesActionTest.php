<?php

declare(strict_types=1);

use App\Domains\Messages\Actions\V1\GetMessagesAction;
use App\Domains\Messages\Models\Message;
use App\Domains\Messages\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new GetMessagesAction;
    $this->user = User::factory()->create();
    $this->room = Room::factory()->create();
    $this->room->users()->attach($this->user->id, ['joined_at' => now()]);
});

it('retrieves messages for a room', function () {
    Message::factory()->count(5)->create([
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ]);

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ];

    $result = $this->action->execute($data);

    expect($result->messages)->toHaveCount(5)
        ->and($result->total)->toBe(5);
});

it('paginates messages correctly', function () {
    Message::factory()->count(60)->create([
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ]);

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
        'per_page' => 20,
        'page' => 2,
    ];

    $result = $this->action->execute($data);

    expect($result->messages)->toHaveCount(20)
        ->and($result->total)->toBe(60)
        ->and($result->currentPage)->toBe(2)
        ->and($result->lastPage)->toBe(3);
});

it('throws exception when user is not in room', function () {
    $otherUser = User::factory()->create();

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $otherUser->id,
    ];

    expect(fn () => $this->action->execute($data))
        ->toThrow(Exception::class, 'User is not a member of this room');
});

it('orders messages by created_at desc', function () {
    $oldMessage = Message::factory()->create([
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
        'content' => 'Old message',
        'created_at' => now()->subDay(),
    ]);

    $newMessage = Message::factory()->create([
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
        'content' => 'New message',
        'created_at' => now(),
    ]);

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ];

    $result = $this->action->execute($data);

    expect($result->messages[0]->content)->toBe('New message')
        ->and($result->messages[1]->content)->toBe('Old message');
});
