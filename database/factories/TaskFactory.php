<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'is_done' => $this->faker->boolean(20),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function done(): self
    {
        return $this->state(fn () => ['is_done' => true]);
    }

    public function dueToday(): self
    {
        return $this->state(fn () => ['due_date' => now()->startOfDay()]);
    }
}
