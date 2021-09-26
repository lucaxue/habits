<?php

namespace HabitTracking\Infrastructure\Eloquent;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Infrastructure\Reflection;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Domain\Contracts\HabitRepository as HabitRepositoryInterface;

class HabitRepository implements HabitRepositoryInterface
{
    public function all(): array
    {
        $habits = EloquentHabit::where('user_id', auth()->id())->get();

        return $habits
            ->map(fn ($habit) => $this->transformIntoHabit($habit))
            ->all();
    }

    public function find(HabitId $id): Habit
    {
        $habit = EloquentHabit::findOrFail($id);

        if (auth()->id() !== $habit->user->id) {
            throw new \Exception;
        }

        return $this->transformIntoHabit($habit);
    }

    public function save(Habit $habit): void
    {
        $reflection = Reflection::for($habit);

        $lastCompleted = $reflection->get('lastCompleted');
        $lastIncompleted = $reflection->get('lastIncompleted');

        EloquentHabit::updateOrCreate([
            'id' => $habit->id()
        ], [
            'name' => $habit->name(),
            'streak' => $habit->streak(),
            'frequency' => $habit->frequency(),
            'last_completed' => $lastCompleted,
            'last_incompleted' => $lastIncompleted,
            'stopped' => $habit->stopped(),
            'user_id' => auth()->id()
        ]);
    }

    private function transformIntoHabit(
        EloquentHabit $habit
    ): Habit {

        $instance = Habit::start(
            HabitId::fromString($habit->id),
            $habit->name,
            new HabitFrequency(
                $habit->frequency->type,
                $habit->frequency->days
            ),
        );

        Reflection::for($instance)
            ->mutate('lastCompleted', $habit->lastCompleted)
            ->mutate('lastIncompleted', $habit->lastIncompleted)
            ->mutate('streak', HabitStreak::fromString($habit->streak));

        return $instance;
    }
}
