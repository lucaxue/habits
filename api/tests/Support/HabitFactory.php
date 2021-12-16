<?php

namespace Tests\Support;

use App\Models\User;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Domain\Habit;

class HabitFactory
{
    public static function start(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::start(array_merge([
            'authorId' => User::factory()->create()->id,
        ], $overrides));

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function create(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::create(array_merge([
            'authorId' => User::factory()->create()->id,
        ], $overrides));

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function completed(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::completed(array_merge([
            'authorId' => User::factory()->create()->id,
        ], $overrides));

        resolve(HabitRepository::class)->save($habit);

        return $habit;
    }

    public static function incompleted(
        array $overrides = []
    ) : Habit {

        $habit = HabitModelFactory::incompleted(array_merge([
            'authorId' => User::factory()->create()->id,
        ], $overrides));

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
            ) {}

            /** @return Habit[] */
            public function start(array $overrides = []) : array
            {
                $habits = [];

                for ($i = 0; $i < $this->count; $i++) {
                    $habits[] = HabitFactory::start($overrides);
                }

                return $habits;
            }

            /** @return Habit[] */
            public function completed(array $overrides = []) : array
            {
                $habits = [];

                for ($i = 0; $i < $this->count; $i++) {
                    $habits[] = HabitFactory::completed($overrides);
                }

                return $habits;
            }
        };
    }
}
