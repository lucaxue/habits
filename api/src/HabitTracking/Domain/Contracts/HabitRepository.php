<?php

namespace HabitTracking\Domain\Contracts;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;

interface HabitRepository
{
    /** @return Collection<Habit> */
    public function all(int $authorId): Collection;
    /** @return Collection<Habit> */
    public function forToday(int $authorId): Collection;
    public function find(HabitId $id): ?Habit;
    public function save(Habit $habit): void;
}
