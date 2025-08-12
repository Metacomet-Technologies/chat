<?php

declare(strict_types=1);

use App\Domains\Messages\Models\Message;
use App\Domains\Messages\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->room = Room::factory()->create();
    $this->room->users()->attach($this->user->id, ['joined_at' => now()]);

    Sanctum::actingAs($this->user);
});

it('can send a message to a room', function () {
    $response = $this->postJson("/api/v1/rooms/{$this->room->id}/messages", [
        'content' => 'Hello, world!',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'id',
            'roomId',
            'userId',
            'content',
            'type',
            'createdAt',
            'updatedAt',
        ])
        ->assertJson([
            'content' => 'Hello, world!',
            'roomId' => $this->room->id,
            'userId' => $this->user->id,
        ]);

    $this->assertDatabaseHas('messages', [
        'content' => 'Hello, world!',
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ]);
});

it('cannot send message to room user is not in', function () {
    $otherRoom = Room::factory()->create();

    $response = $this->postJson("/api/v1/rooms/{$otherRoom->id}/messages", [
        'content' => 'Hello',
    ]);

    $response->assertStatus(500);
});

it('can retrieve messages from a room', function () {
    Message::factory()->count(5)->create([
        'room_id' => $this->room->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->getJson("/api/v1/rooms/{$this->room->id}/messages");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'messages',
            'total',
            'perPage',
            'currentPage',
            'lastPage',
        ])
        ->assertJsonCount(5, 'messages');
});

it('cannot retrieve messages from room user is not in', function () {
    $otherRoom = Room::factory()->create();

    $response = $this->getJson("/api/v1/rooms/{$otherRoom->id}/messages");

    $response->assertStatus(500);
});

it('validates message content is required', function () {
    $response = $this->postJson("/api/v1/rooms/{$this->room->id}/messages", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['content']);
});

it('validates message content max length', function () {
    $longContent = str_repeat('a', 5001);

    $response = $this->postJson("/api/v1/rooms/{$this->room->id}/messages", [
        'content' => $longContent,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['content']);
});

it('requires authentication', function () {
    Sanctum::actingAs(new User);

    $response = $this->getJson("/api/v1/rooms/{$this->room->id}/messages");

    $response->assertStatus(401);
});
