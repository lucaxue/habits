<?php

namespace Database\Factories;

use App\Models\User;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Presentation\Habit;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitFactory extends Factory
{
    protected $model = Habit::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'author_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'streak' => 'P'.rand(0, 2).'Y'.rand(0, 11).'M'.rand(0, 29).'D',
            'frequency' => $this->faker->randomElement([
                new HabitFrequency('daily'),
                new HabitFrequency('weekly', [1, 2, 3]),
            ]),
            'last_completed' => $this->faker->dateTime(),
        ];
    }
}
