<?php

namespace HabitTracking\Application;

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\Contracts\HabitRepository;

class HabitService
{
    public function __construct(
        private HabitRepository $repository
    ) {
    }

    /** @return Habit[] */
    public function retrieveHabits(): array
    {
        return $this->repository->all();
    }

    public function retrieveHabitsForToday(): array
    {
        $habits = $this->repository->all();

        return array_filter(
            $habits,
            fn (Habit $habit) => $habit->frequency()->includesToday()
        );
    }

    public function retrieveHabit(string $id): Habit
    {
        return $this->repository->find(HabitId::fromString($id));
    }

    public function startHabit(
        string $name,
        array $frequency
    ): Habit {

        $habit = Habit::start(
            HabitId::generate(),
            $name,
            new HabitFrequency(...$frequency),
        );

        $this->repository->save($habit);

        return $habit;
    }

    public function markHabitAsComplete(
        string $id
    ): Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        $habit->markAsComplete();

        $this->repository->save($habit);

        return $habit;
    }

    public function markHabitAsIncomplete(
        string $id
    ): Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        $habit->markAsIncomplete();

        $this->repository->save($habit);

        return $habit;
    }

    public function editHabit(
        string $id,
        string $name,
        array $frequency,
    ): Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        $habit->edit($name, new HabitFrequency(
            $frequency['type'],
            $frequency['days']
        ));

        $this->repository->save($habit);

        return $habit;
    }

    public function stopHabit(
        string $id
    ): Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        $habit->stop();

        $this->repository->save($habit);

        return $habit;
    }
}
