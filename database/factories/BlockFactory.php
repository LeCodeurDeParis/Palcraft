<?php

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->slug(2, false);

        return [
            'identifier' => 'minecraft:'.str_replace('-', '_', $name),
            'name' => ucwords(str_replace('-', ' ', $name)),
            'texture' => null,
            'color' => fake()->hexColor(),
        ];
    }
}
