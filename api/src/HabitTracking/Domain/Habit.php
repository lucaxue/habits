<?php

namespace HabitTracking\Domain;

use HabitTracking\Domain\Exceptions\InactiveHabitDay;
use HabitTracking\Domain\Exceptions\HabitHasNotStarted;
use HabitTracking\Domain\Exceptions\HabitCompletedConsecutively;
use HabitTracking\Domain\Exceptions\HabitStreaksIncrementedConsecutively;

class Habit
{
    private function __construct(
        private HabitId $id,
        private HabitName $name,
        private HabitStartDate $startDate,
        private HabitFrequency $frequency,
        private HabitStreaks $streaks,
        private ?string $lastCompleted = null
    ) {
    }

    public static function plan(
        HabitId $id,
        HabitName $name,
        HabitStartDate $startDate,
        HabitFrequency $frequency,
    ): self {

        return new self(
            $id,
            $name,
            $startDate,
            $frequency,
            new HabitStreaks
        );
    }

    public function setStreaks(HabitStreaks $streaks): void
    {
        $this->streaks = $streaks;
        $this->lastCompleted = $streaks->lastAdded()->toDateString();
    }

    /** @throws HabitCompletedConsecutively */
    public function markAsComplete(): void
    {
        if ($this->startDate()->isFuture()) {
            throw new HabitHasNotStarted($this->startDate());
        }

        if (!$this->frequency()->hasTodayAsActive()) {
            throw new InactiveHabitDay($this->frequency());
        }

        try {
            $this->streaks()->increment();
        } catch (HabitStreaksIncrementedConsecutively $e) {
            throw new HabitCompletedConsecutively;
        } finally {
            $this->lastCompleted = now()->toDateString();
        }
    }

    public function markAsIncomplete(): void
    {
        $this->streaks()->reset();
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

    public function streaks(): HabitStreaks
    {
        return $this->streaks;
    }

    public function lastCompleted(): ?string
    {
        return $this->lastCompleted;
    }
}
