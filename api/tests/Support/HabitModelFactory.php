<?php

namespace Tests\Support;

use Carbon\CarbonImmutable;
use Faker\Factory as Faker;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use Illuminate\Support\Collection;

class HabitModelFactory
{
    public static function start(
        array $overrides = []
    ) : Habit {

        $faker = Faker::create();

        $defaults = [
            'id' => HabitId::generate(),
            'authorId' => rand(1, 100),
            'name' => $faker->sentence(),
            'frequency' => $faker->randomElement([
                new HabitFrequency('daily'),
                new HabitFrequency('weekly', [
                    HabitFrequency::MONDAY,
                    HabitFrequency::TUESDAY,
                    HabitFrequency::WEDNESDAY,
                ]),
            ]),
        ];

        $attributes = array_merge($defaults, $overrides);

        return Habit::start(...$attributes);
    }

    public static function create(
        array $overrides = []
    ) : Habit {

        $faker = Faker::create();

        $defaults = [
            'id' => HabitId::generate(),
            'authorId' => rand(1, 100),
            'name' => $faker->sentence(),
            'frequency' => $faker->randomElement([
                new HabitFrequency('daily'),
                new HabitFrequency('weekly', [
                    HabitFrequency::MONDAY,
                    HabitFrequency::TUESDAY,
                    HabitFrequency::WEDNESDAY,
                ]),
            ]),
        ];

        $attributes = array_merge($defaults, $overrides);

        return new Habit(...$attributes);
    }

    public static function completed(
        array $overrides = []
    ) : Habit {

        $attributes = array_merge($overrides, [
            'lastCompleted' => CarbonImmutable::now(),
            'streak' => $overrides['streak'] ?? new HabitStreak(0, 0, 1),
        ]);

        return self::create($attributes);
    }

    public static function incompleted(
        array $overrides = []
    ) : Habit {

        $attributes = array_merge($overrides, [
            'lastCompleted' => null,
            'streak' => new HabitStreak(),
        ]);

        return self::create($attributes);
    }

    public static function count(
        int $count
    ) {
        return new class($count) {
            public function __construct(
                private int $count
            ) {}

            /**
             * @param array $overrides
             * @return Collection<Habit>
             */
            public function start(
                array $overrides = []
            ) : Collection {

                $habits = [];

                for ($i = 0; $i < $this->count; $i++) {
                    $habits[] = HabitModelFactory::start($overrides);
                }

                return collect($habits);
            }
        };
    }
}
