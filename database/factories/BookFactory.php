<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['por_leer', 'leyendo', 'leido', 'abandonado']);
        $startedAt = null;
        $finishedAt = null;

        if ($status !== 'por_leer') {
            $startedAt = $this->faker->dateTimeBetween('-2 years', '-1 month');
        }

        if ($status === 'leido') {
            $finishedAt = $this->faker->dateTimeBetween($startedAt, 'now');
        }

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'published_year' => $this->faker->optional()->year(),
            'status' => $status,
            'rating' => $status === 'leido' ? $this->faker->optional()->numberBetween(1, 5) : null,
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
            'review' => $status === 'leido' ? $this->faker->optional()->paragraph() : null,
        ];
    }
}
