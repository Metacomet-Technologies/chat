<?php

namespace Database\Factories\Domains\Messages\Models;

use App\Domains\Messages\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Messages\Models\Room>
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
        return [
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['public', 'private', 'direct']),
            'is_private' => fake()->boolean(30),
        ];
    }

    /**
     * Indicate that the room is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'public',
            'is_private' => false,
        ]);
    }

    /**
     * Indicate that the room is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'private',
            'is_private' => true,
        ]);
    }

    /**
     * Indicate that the room is direct.
     */
    public function direct(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'direct',
            'is_private' => true,
        ]);
    }
}
