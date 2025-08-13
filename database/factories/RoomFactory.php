<?php

namespace Database\Factories;

use App\Domains\Rooms\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Rooms\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->catchPhrase();
        
        return [
            'name' => $name,
            'description' => fake()->optional()->sentence(),
            'slug' => Str::slug($name),
            'is_public' => fake()->boolean(80), // 80% chance of being public
            'created_by_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the room is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }

    /**
     * Indicate that the room is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }
}