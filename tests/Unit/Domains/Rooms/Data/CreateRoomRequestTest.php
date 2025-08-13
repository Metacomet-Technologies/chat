<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Rooms\Data;

use App\Domains\Rooms\Data\V1\CreateRoomRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreateRoomRequestTest extends TestCase
{
    public function test_valid_room_request(): void
    {
        $data = [
            'name' => 'Valid Room',
            'description' => 'A valid room description',
            'slug' => 'valid-room',
            'is_public' => true,
        ];

        $request = CreateRoomRequest::from($data);

        $this->assertEquals('Valid Room', $request->name);
        $this->assertEquals('A valid room description', $request->description);
        $this->assertEquals('valid-room', $request->slug);
        $this->assertTrue($request->is_public);
    }

    public function test_room_request_validation_rules(): void
    {
        $data = [
            'name' => '',
            'slug' => 'ab', // Too short
            'description' => str_repeat('a', 501), // Too long
        ];

        $validator = Validator::make($data, CreateRoomRequest::rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('slug', $validator->errors()->toArray());
        $this->assertArrayHasKey('description', $validator->errors()->toArray());
    }

    public function test_room_request_with_defaults(): void
    {
        $data = [
            'name' => 'Default Room',
            'slug' => 'default-room',
        ];

        $request = CreateRoomRequest::from($data);

        $this->assertEquals('Default Room', $request->name);
        $this->assertEquals('default-room', $request->slug);
        $this->assertNull($request->description);
        $this->assertTrue($request->is_public); // Default value
    }
}