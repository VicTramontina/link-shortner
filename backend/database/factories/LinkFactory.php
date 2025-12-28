<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => fake()->url(),
            'slug' => Str::random(rand(6, 8)),
            'title' => fake()->sentence(3),
            'access_count' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the link has no accesses.
     */
    public function withoutAccesses(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_count' => 0,
        ]);
    }

    /**
     * Indicate that the link is deleted.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }
}
