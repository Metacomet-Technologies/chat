<?php

declare(strict_types=1);

use App\Domains\Messages\Actions\V1\SendMessageAction;
use App\Domains\Messages\Models\Room;
use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new SendMessageAction;
    $this->user = User::factory()->create();
    $this->room = Room::factory()->create();
    $this->room->users()->attach($this->user->id, ['joined_at' => now()]);
});

it('creates a message successfully', function () {
    Event::fake();

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
        'content' => 'Hello, world!',
        'type' => 'text',
    ];

    $result = $this->action->execute($data);

    expect($result->content)->toBe('Hello, world!')
        ->and($result->roomId)->toBe($this->room->id)
        ->and($result->userId)->toBe($this->user->id)
        ->and($result->type)->toBe('text');

    Event::assertDispatched(MessageSent::class);
});

it('throws exception when user is not in room', function () {
    $otherUser = User::factory()->create();

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $otherUser->id,
        'content' => 'Hello',
    ];

    expect(fn () => $this->action->execute($data))
        ->toThrow(Exception::class, 'User is not a member of this room');
});

it('updates user last read timestamp', function () {
    Event::fake();

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
        'content' => 'Hello',
    ];

    $this->action->execute($data);

    $pivot = $this->room->users()->where('user_id', $this->user->id)->first()->pivot;

    expect($pivot->last_read_at)->not->toBeNull();
});

it('saves metadata when provided', function () {
    Event::fake();

    $metadata = ['attachment_id' => 123];

    $data = [
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
        'content' => 'Hello',
        'metadata' => $metadata,
    ];

    $result = $this->action->execute($data);

    expect($result->metadata)->toBe($metadata);
});
