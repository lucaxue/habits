<?php

namespace HabitTracking\Domain\Contracts;

use HabitTracking\Domain\Exceptions\HabitNotFound;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;

interface HabitRepository
{
    /**
     * @param int $authorId
     * @param array $filters ['forToday' => bool]
     * @return Collection<Habit>
     */
    public function all(int $authorId, array $filters = []) : Collection;

    /**
     * @param HabitId $id
     * @return ?Habit
     * @throws HabitNotFound
     */
    public function find(HabitId $id) : ? Habit;

    public function save(Habit $habit) : void;
}
