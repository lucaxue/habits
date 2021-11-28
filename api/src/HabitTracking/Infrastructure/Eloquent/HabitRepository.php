<?php

namespace HabitTracking\Infrastructure\Eloquent;

use HabitTracking\Domain\Contracts\HabitRepository as HabitRepositoryInterface;
use HabitTracking\Domain\Exceptions\HabitNotFound;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;

class HabitRepository implements HabitRepositoryInterface
{
    public function find(HabitId $id) : ? Habit
    {
        $habit = EloquentHabit::find($id);

        if ( ! $habit) {
            throw new HabitNotFound($id);
        }

        return $habit->toModel();
    }

    public function save(Habit $habit) : void
    {
        EloquentHabit::updateOrCreate([
            'id' => $habit->id(),
        ], [
            'author_id' => $habit->authorId(),
            'name' => $habit->name(),
            'streak' => $habit->streak(),
            'frequency' => $habit->frequency(),
            'last_completed' => $habit->lastCompleted(),
            'stopped' => $habit->stopped(),
        ]);
    }
}
