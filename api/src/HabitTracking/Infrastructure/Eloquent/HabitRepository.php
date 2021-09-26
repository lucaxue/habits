<?php

namespace HabitTracking\Infrastructure\Eloquent;

use ReflectionObject;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\Contracts\HabitRepository as HabitRepositoryInterface;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;

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
        $payload = json_decode(json_encode($habit), true);

        EloquentHabit::create(array_merge(
            $payload,
            ['user_id' => auth()->id()]
        ));
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

        PropertyMutator::for($instance)
            ->mutate('lastCompleted', $habit->lastCompleted)
            ->mutate('lastIncompleted', $habit->lastIncompleted)
            ->mutate('streak', HabitStreak::fromString($habit->streak));

        return $instance;
    }
}

class PropertyMutator
{
    private function __construct(
        private ReflectionObject $reflection,
        private object $instance,
    ) {
    }

    public static function for(object $instance): self
    {
        return new self(
            new ReflectionObject($instance),
            $instance,
        );
    }

    public function mutate(string $name, mixed $value): self
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->instance, $value);

        return $this;
    }
}
