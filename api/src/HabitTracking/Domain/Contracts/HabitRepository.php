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
     * @return Collection<Habit>
     */
    public function all(int $authorId): Collection;

    /**
     * @param int $authorId
     * @return Collection<Habit>
     */
    public function forToday(int $authorId): Collection;

    /**
     * @param HabitId $id
     * @return Habit|null
     * @throws HabitNotFoundException
     */
    public function find(HabitId $id): ?Habit;
    public function save(Habit $habit): void;
}
