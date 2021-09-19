<?php

namespace HabitTracking\Domain;

use HabitTracking\Domain\Exceptions\HabitHasNotStarted;

class Habit
{
    private int $streaks = 0;

    private function __construct(
        private HabitId $id,
        private HabitName $name,
        private HabitStartDate $startDate,
        private HabitFrequency $frequency,
    ) {
    }

    public static function plan(
        HabitId $id,
        HabitName $name,
        HabitStartDate $startDate,
        HabitFrequency $frequency,
    ): self {
        return new self($id, $name, $startDate, $frequency);
    }

    public function id(): HabitId
    {
        return $this->id;
    }

    public function name(): HabitName
    {
        return $this->name;
    }

    public function startDate(): HabitStartDate
    {
        return $this->startDate;
    }

    public function frequency(): HabitFrequency
    {
        return $this->frequency;
    }

    public function markAsComplete(): void
    {
        if ($this->startDate()->isFuture()) {
            throw new HabitHasNotStarted($this->startDate());
        }

        // if (! $this->frequency()->hasTodayAsActive()) {
        //     throw new HabitDoesNotIncludeToday($this->startDate());
        // }

        $this->streaks++;
        // $this->frequency()->mark();
    }

    public function streaks(): int
    {
        return $this->streaks;
    }
}
