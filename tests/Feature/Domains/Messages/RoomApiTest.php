<?php

declare(strict_types=1);

use App\Domains\Messages\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can create a room', function () {
    $response = $this->postJson('/api/v1/rooms', [
        'name' => 'Test Room',
        'type' => 'public',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'name',
            'type',
            'isPrivate',
            'createdAt',
            'updatedAt',
        ])
        ->assertJson([
            'name' => 'Test Room',
            'type' => 'public',
            'isPrivate' => false,
        ]);

    $this->assertDatabaseHas('rooms', [
        'name' => 'Test Room',
        'type' => 'public',
    ]);

    $room = Room::where('name', 'Test Room')->first();
    $this->assertTrue($room->hasUser($this->user));
});

it('can list user rooms', function () {
    $rooms = Room::factory()->count(3)->create();

    foreach ($rooms as $room) {
        $room->users()->attach($this->user->id, ['joined_at' => now()]);
    }

    $response = $this->getJson('/api/v1/rooms');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

it('can get room details', function () {
    $room = Room::factory()->create();
    $room->users()->attach($this->user->id, ['joined_at' => now()]);

    $response = $this->getJson("/api/v1/rooms/{$room->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $room->id,
            'name' => $room->name,
        ]);
});

it('cannot get details of room user is not in', function () {
    $room = Room::factory()->create();

    $response = $this->getJson("/api/v1/rooms/{$room->id}");

    $response->assertStatus(403);
});

it('can join a public room', function () {
    $room = Room::factory()->public()->create();

    $response = $this->postJson("/api/v1/rooms/{$room->id}/join");

    $response->assertStatus(200);

    $this->assertTrue($room->fresh()->hasUser($this->user));
});

it('cannot join a private room', function () {
    $room = Room::factory()->private()->create();

    $response = $this->postJson("/api/v1/rooms/{$room->id}/join");

    $response->assertStatus(500);
});

it('cannot join a room twice', function () {
    $room = Room::factory()->public()->create();
    $room->users()->attach($this->user->id, ['joined_at' => now()]);

    $response = $this->postJson("/api/v1/rooms/{$room->id}/join");

    $response->assertStatus(500);
});

it('validates room name is required', function () {
    $response = $this->postJson('/api/v1/rooms', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('validates room type must be valid', function () {
    $response = $this->postJson('/api/v1/rooms', [
        'name' => 'Test Room',
        'type' => 'invalid',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

it('requires authentication', function () {
    Sanctum::actingAs(new User);

    $response = $this->getJson('/api/v1/rooms');

    $response->assertStatus(401);
});
