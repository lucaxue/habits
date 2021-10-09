<?php

namespace HabitTracking\Infrastructure\Eloquent;

use HabitTracking\Domain\Habit;
use Illuminate\Auth\AuthManager;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Domain\Contracts\HabitRepository as HabitRepositoryInterface;

class HabitRepository implements HabitRepositoryInterface
{
    public function __construct(
        private AuthManager $auth
    ) {
    }

    public function all(): array
    {
        $habits = EloquentHabit::where('user_id', $this->auth->id())->get();

        return $habits->map(fn ($habit) => new Habit(
            HabitId::fromString($habit->id),
            $habit->name,
            new HabitFrequency(...(array) $habit->frequency),
            HabitStreak::fromString($habit->streak),
            $habit->stopped,
            $habit->last_completed,
            $habit->last_incompleted,
        ))->all();
    }

    public function forToday(): array
    {
        $habits = EloquentHabit::query()
            ->where('user_id', $this->auth->id())
            ->where(function ($query) {
                $query
                    ->whereJsonContains('frequency->days', [now()->dayOfWeek])
                    ->orWhere('frequency->days', null);
            })
            ->get();

        return $habits->map(fn ($habit) => new Habit(
            HabitId::fromString($habit->id),
            $habit->name,
            new HabitFrequency(...(array) $habit->frequency),
            HabitStreak::fromString($habit->streak),
            $habit->stopped,
            $habit->last_completed,
            $habit->last_incompleted,
        ))->all();
    }

    public function find(HabitId $id): Habit
    {
        $habit = EloquentHabit::findOrFail($id);

        if ($this->auth->id() !== $habit->user->id) {
            throw new \Exception;
        }

        return new Habit(
            HabitId::fromString($habit->id),
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
            'id' => $habit->id()
        ], [
            'name' => $habit->name(),
            'streak' => $habit->streak(),
            'frequency' => $habit->frequency(),
            'last_completed' => $habit->lastCompleted(),
            'last_incompleted' => $habit->lastIncompleted(),
            'stopped' => $habit->stopped(),
            'user_id' => $this->auth->id()
        ]);
    }
}
