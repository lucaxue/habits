<?php

namespace HabitTracking\Domain\Contracts;

use HabitTracking\Domain\Exceptions\HabitNotFound;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;

interface HabitRepository
{
    /**
     * @param HabitId $id
     * @return ?Habit
     * @throws HabitNotFound
     */
    public function find(HabitId $id) : ? Habit;

    public function save(Habit $habit) : void;
}
