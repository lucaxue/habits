<?php

namespace HabitTracking\Infrastructure\Eloquent;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Domain\Contracts\HabitRepository as HabitRepositoryInterface;

class HabitRepository implements HabitRepositoryInterface
{
    public function all(
        int $authorId,
        bool $forToday = false
    ): Collection {

        $query =  EloquentHabit::where('author_id', $authorId);

        if ($forToday) {
            $query->where(function ($query) {
                $query
                    ->whereJsonContains('frequency->days', [now()->dayOfWeek])
                    ->orWhere('frequency->type', 'daily');
            });
        }

        return $query->get()->map->toModel();
    }

    public function find(HabitId $id): ?Habit
    {
        $habit = EloquentHabit::find($id);

        if (!$habit) {
            throw new HabitNotFoundException($id);
        }

        return $habit->toModel();
    }

    public function save(Habit $habit): void
    {
        EloquentHabit::updateOrCreate([
            'id' => $habit->id(),
        ], [
            'author_id' => $habit->authorId(),
            'name' => $habit->name(),
            'streak' => $habit->streak(),
            'frequency' => $habit->frequency(),
            'last_completed' => $habit->lastCompleted(),
            'last_incompleted' => $habit->lastIncompleted(),
            'stopped' => $habit->stopped(),
        ]);
    }
}
