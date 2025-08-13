<?php

declare(strict_types=1);

namespace Tests\Feature\Domains\Rooms;

use App\Domains\Rooms\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_public_rooms(): void
    {
        $publicRooms = Room::factory()->public()->count(3)->create();
        $privateRooms = Room::factory()->private()->count(2)->create();
        
        // Debug: Check what was created
        $this->assertCount(3, Room::where('is_private', false)->get());
        $this->assertCount(2, Room::where('is_private', true)->get());

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/rooms');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_room(): void
    {
        $roomData = [
            'name' => 'Test Room',
            'description' => 'A test room',
            'slug' => 'test-room',
            'is_public' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/rooms', $roomData);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'Test Room',
                'slug' => 'test-room',
            ]);

        $this->assertDatabaseHas('rooms', [
            'name' => 'Test Room',
            'slug' => 'test-room',
            'created_by_id' => $this->user->id,
        ]);

        // Check creator is added as admin
        $this->assertDatabaseHas('room_members', [
            'room_id' => $response->json('id'),
            'user_id' => $this->user->id,
            'role' => 'admin',
        ]);
    }

    public function test_can_join_public_room(): void
    {
        $room = Room::factory()->public()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/rooms/join', [
                'room_id' => $room->id,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('room_members', [
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'role' => 'member',
        ]);
    }

    public function test_cannot_join_private_room(): void
    {
        $room = Room::factory()->private()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/rooms/join', [
                'room_id' => $room->id,
            ]);

        $response->assertForbidden();
    }

    public function test_can_leave_room(): void
    {
        $room = Room::factory()->create();
        $room->addMember($this->user);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/rooms/{$room->id}/leave");

        $response->assertOk();

        $this->assertDatabaseMissing('room_members', [
            'room_id' => $room->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_creator_cannot_leave_room(): void
    {
        $room = Room::factory()->create(['created_by_id' => $this->user->id]);
        $room->addMember($this->user, 'admin');

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/rooms/{$room->id}/leave");

        $response->assertForbidden();
    }

    public function test_can_get_room_members(): void
    {
        $room = Room::factory()->create();
        $room->addMember($this->user);
        
        $otherUsers = User::factory()->count(3)->create();
        foreach ($otherUsers as $user) {
            $room->addMember($user);
        }

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/rooms/{$room->id}/members");

        $response->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_creator_can_delete_room(): void
    {
        $room = Room::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/rooms/{$room->id}");

        $response->assertOk();

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
        ]);
    }

    public function test_non_creator_cannot_delete_room(): void
    {
        $room = Room::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/rooms/{$room->id}");

        $response->assertForbidden();
    }
}