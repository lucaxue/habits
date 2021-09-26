<?php

namespace Tests\Support;

use HabitTracking\Domain\Habit;
use Tests\Support\HabitInstanceFactory;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Infrastructure\Reflection;

class HabitFactory
{
    public static function start(
        array $overrides = []
    ): Habit {

        $instanceOverrides = array_diff_key($overrides, [
            'streak' => null,
            'completed' => null
        ]);

        $habit = HabitInstanceFactory::start($instanceOverrides);

        if (array_key_exists('streaks', $overrides)) {
            Reflection::for($habit)
                ->mutate('streak', $overrides['streak']);
        }

        if (array_key_exists('completed', $overrides)) {
            Reflection::for($habit)
                ->mutate(
                    'lastCompleted',
                    $overrides['completed'] ? now() : null
                );
        }

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function many()
    {
        return new class
        {
            /**
             * @param array $manyOverrides
             * @return Habit[]
             */
            public function start(array $manyOverrides): array
            {
                $habits = [];

                foreach ($manyOverrides as $overrides) {
                    $habits[] = HabitFactory::start($overrides);
                }

                return $habits;
            }
        };
    }

    public static function count(int $count)
    {
        return new class($count)
        {
            public function __construct(
                private int $count
            ) {
            }

            /**
             * @param array $overrides
             * @return Habit[]
             */
            public function start(
                array $overrides = []
            ): array {

                $habits = HabitInstanceFactory::count($this->count)->start($overrides);

                foreach ($habits as $habit) {
                    resolve(HabitRepository::class)->save($habit);
                }

                return $habits;
            }
        };
    }
}
