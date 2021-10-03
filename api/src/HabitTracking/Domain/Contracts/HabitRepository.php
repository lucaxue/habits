<?php

namespace HabitTracking\Domain\Contracts;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;

interface HabitRepository
{
    /** @return Habit[] */
    public function all(): array;

    /** @return Habit[] */
    public function forToday(): array;

    /** @throws HabitNotFoundException */
    public function find(HabitId $id): Habit;

    public function save(Habit $habit): void;
}
