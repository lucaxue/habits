<?php

namespace HabitTracking\Application;

use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Domain\Exceptions\HabitDoesNotBelongToAuthor;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;

class HabitService
{
    public function __construct(
        private HabitRepository $repository
    ) {
    }

    /** @return Collection<Habit> */
    public function retrieveHabits(int $authorId) : Collection
    {
        return $this->repository->all($authorId);
    }

    /** @return Collection<Habit> */
    public function retrieveHabitsForToday(int $authorId) : Collection
    {
        return $this->repository->all($authorId, ['forToday' => true]);
    }

    public function retrieveHabit(string $id, int $authorId) : Habit
    {
        $habit = $this->repository->find(HabitId::fromString($id));

        if ($habit->authorId() !== $authorId) {
            throw new HabitDoesNotBelongToAuthor();
        }

        return $habit;
    }

    public function startHabit(
        string $name,
        array $frequency,
        int $authorId,
    ) : Habit {

        $habit = Habit::start(
            HabitId::generate(),
            $authorId,
            $name,
            new HabitFrequency(...$frequency),
        );

        $this->repository->save($habit);

        return $habit;
    }

    public function markHabitAsComplete(
        string $id,
        int $authorId
    ) : Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        if ($habit->authorId() !== $authorId) {
            throw new HabitDoesNotBelongToAuthor();
        }

        $habit->markAsComplete();

        $this->repository->save($habit);

        return $habit;
    }

    public function markHabitAsIncomplete(
        string $id,
        int $authorId
    ) : Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        if ($habit->authorId() !== $authorId) {
            throw new HabitDoesNotBelongToAuthor();
        }

        $habit->markAsIncomplete();

        $this->repository->save($habit);

        return $habit;
    }

    public function editHabit(
        string $id,
        string $name,
        array $frequency,
        int $authorId
    ) : Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        if ($habit->authorId() !== $authorId) {
            throw new HabitDoesNotBelongToAuthor();
        }

        $habit->edit($name, new HabitFrequency(...$frequency));

        $this->repository->save($habit);

        return $habit;
    }

    public function stopHabit(
        string $id,
        int $authorId,
    ) : Habit {

        $habit = $this->repository->find(HabitId::fromString($id));

        if ($habit->authorId() !== $authorId) {
            throw new HabitDoesNotBelongToAuthor();
        }

        $habit->stop();

        $this->repository->save($habit);

        return $habit;
    }
}
