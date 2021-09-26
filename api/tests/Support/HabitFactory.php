<?php

namespace Tests\Support;

use Faker\Factory as Faker;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitFrequency;

class HabitFactory
{
    public static function start(
        array $overrides
    ): Habit {

        $faker = Faker::create();

        $defaults = [
            'id' => HabitId::generate(),
            'name' => $faker->sentence(),
            'frequency' => $faker->randomElement([
                new HabitFrequency('daily'),
                new HabitFrequency('weekly', [1, 2, 3])
            ])
        ];

        $attributes = array_merge($defaults, $overrides);

        return Habit::start(...$attributes);
    }

    public static function completed(
        array $overrides
    ): Habit {

        $habit = self::start($overrides);

        $habit->markAsComplete();

        return $habit;
    }
}
