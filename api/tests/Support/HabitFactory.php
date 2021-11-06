<?php

namespace Tests\Support;

use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Domain\Habit;

class HabitFactory
{
    public static function start(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::start($overrides);

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function create(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::create($overrides);

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function completed(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::completed($overrides);

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function incompleted(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::incompleted($overrides);

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function many()
    {
        return new class() {
            /** @return Habit[] */
            public function start(array $manyOverrides) : array
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
        return new class($count) {
            public function __construct(
                private int $count
            ) {
            }

            /** @return Habit[] */
            public function start(
                array $overrides = []
            ) : array {

                $habits = [];

                for ($i = 0; $i < $this->count; $i++) {
                    $habits[] = HabitFactory::start($overrides);
                }

                return $habits;
            }
        };
    }
}
