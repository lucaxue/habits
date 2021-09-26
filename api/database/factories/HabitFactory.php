<?php

namespace Database\Factories;

use App\Models\User;
use HabitTracking\Infrastructure\Habit;
use HabitTracking\Domain\HabitFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitFactory extends Factory
{
    protected $model = Habit::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->sentence(3),
            'streak' => 'P'.rand(0, 2).'Y'.rand(0, 11).'M'.rand(0, 29).'D',
            'frequency' => $this->faker->randomElement([
                new HabitFrequency('daily'),
                new HabitFrequency('weekly', [1, 2, 3]),
            ]),
            'last_completed' => $this->faker->dateTime(),
            'last_incompleted' => $this->faker->dateTime(),
            'user_id' => User::factory(),
        ];
    }
}
