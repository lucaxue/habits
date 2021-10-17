<?php

namespace HabitTracking\Infrastructure\Eloquent;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Domain\Contracts\HabitRepository as HabitRepositoryInterface;

class HabitRepository implements HabitRepositoryInterface
{
    public function all(int $authorId): Collection
    {
        $habits = EloquentHabit::where('author_id', $authorId)->get();

        return $habits->map(fn ($habit) => new Habit(
            HabitId::fromString($habit->id),
            $habit->author_id,
            $habit->name,
            new HabitFrequency(...(array) $habit->frequency),
            HabitStreak::fromString($habit->streak),
            $habit->stopped,
            $habit->last_completed,
            $habit->last_incompleted,
        ));
    }

    public function forToday(int $authorId): Collection
    {
        $habits = EloquentHabit::where('author_id', $authorId)
            ->where(function ($query) {
                $query
                    ->whereJsonContains('frequency->days', [now()->dayOfWeek])
                    ->orWhere('frequency->type', 'daily');
            })->get();

        return $habits->map(fn ($habit) => new Habit(
            HabitId::fromString($habit->id),
            $habit->author_id,
            $habit->name,
            new HabitFrequency(...(array) $habit->frequency),
            HabitStreak::fromString($habit->streak),
            $habit->stopped,
            $habit->last_completed,
            $habit->last_incompleted,
        ));
    }

    public function find(HabitId $id): ?Habit
    {
        $habit = EloquentHabit::find($id);

        if (!$habit) { return null; }

        return new Habit(
            HabitId::fromString($habit->id),
            $habit->author_id,
            $habit->name,
            new HabitFrequency(...(array) $habit->frequency),
            HabitStreak::fromString($habit->streak),
            $habit->stopped,
            $habit->last_completed,
            $habit->last_incompleted,
        );
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
