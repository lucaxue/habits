<?php

namespace HabitTracking\Application;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitRepository;

class HabitService
{
    public function __construct(
        private HabitRepository $repository
    ) {
    }

    public function startHabit()
    {
        // make a new habit object
        // persist with repo
        // return habit
    }
}
