<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Rooms\Actions;

use App\Domains\Rooms\Actions\V1\CreateRoomAction;
use App\Domains\Rooms\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateRoomActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_room_with_valid_data(): void
    {
        $user = User::factory()->create();
        $action = new CreateRoomAction();
        
        $data = [
            'name' => 'Test Room',
            'description' => 'Test Description',
            'slug' => 'test-room',
            'is_public' => true,
        ];
        
        $response = $action->execute($data, $user);
        
        $this->assertNotNull($response);
        $this->assertEquals('Test Room', $response->name);
        $this->assertEquals('test-room', $response->slug);
        $this->assertTrue($response->is_public);
        $this->assertEquals($user->id, $response->created_by_id);
        
        // Verify room exists in database
        $this->assertDatabaseHas('rooms', [
            'name' => 'Test Room',
            'slug' => 'test-room',
            'created_by_id' => $user->id,
        ]);
        
        // Verify creator is added as admin
        $this->assertDatabaseHas('room_members', [
            'room_id' => $response->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);
    }

    public function test_creates_room_with_minimal_data(): void
    {
        $user = User::factory()->create();
        $action = new CreateRoomAction();
        
        $data = [
            'name' => 'Minimal Room',
        ];
        
        $response = $action->execute($data, $user);
        
        $this->assertNotNull($response);
        $this->assertEquals('Minimal Room', $response->name);
        $this->assertEquals('minimal-room', $response->slug);
        $this->assertTrue($response->is_public); // Default value
        $this->assertNull($response->description);
    }
}