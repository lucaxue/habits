<?php

namespace HabitTracking\Domain\Contracts;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;

interface HabitRepository
{
    /**
     * @param int $authorId
     * @param bool $forToday
     * @return Collection<Habit>
     */
    public function all(int $authorId, bool $forToday = false): Collection;

    /**
     * @param HabitId $id
     * @return Habit|null
     * @throws HabitNotFoundException
     */
    public function find(HabitId $id): ?Habit;
    public function save(Habit $habit): void;
}
