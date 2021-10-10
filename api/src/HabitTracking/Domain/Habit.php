<?php

namespace HabitTracking\Domain;

use Carbon\CarbonImmutable;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\Exceptions\HabitStoppedException;
use HabitTracking\Domain\Exceptions\HabitAlreadyCompletedException;
use HabitTracking\Domain\Exceptions\HabitAlreadyIncompletedException;

class Habit implements \JsonSerializable
{
    public function __construct(
        private HabitId $id,
        private int $authorId,
        private string $name,
        private HabitFrequency $frequency,
        private ?HabitStreak $streak = null,
        private bool $stopped = false,
        private ?CarbonImmutable $lastCompleted = null,
        private ?CarbonImmutable $lastIncompleted = null,
    ) {

        $this->streak = $streak ?? new HabitStreak;
    }

    public static function start(
        HabitId $id,
        int $authorId,
        string $name,
        HabitFrequency $frequency
    ): self {

        return new self($id, $authorId, $name, $frequency);
    }

    public function markAsComplete(): void
    {
        if ($this->completed()) {
            throw new HabitAlreadyCompletedException;
        }

        $this->lastCompleted = new CarbonImmutable;
        $this->streak()->increment();
    }

    public function markAsIncomplete(): void
    {
        if (!$this->completed()) {
            throw new HabitAlreadyIncompletedException;
        }

        $this->lastIncompleted = new CarbonImmutable;
        $this->streak()->decrement();
    }

    public function edit(
        string $name,
        HabitFrequency $frequency
    ): void {

        $this->name = $name;
        $this->frequency = $frequency;
    }

    public function stop(): void
    {
        if ($this->stopped()) {
            throw new HabitStoppedException;
        }

        $this->stopped = true;
    }

    public function id(): HabitId
    {
        return $this->id;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function frequency(): HabitFrequency
    {
        return $this->frequency;
    }

    public function streak(): HabitStreak
    {
        return $this->streak;
    }

    public function lastIncompleted(): ?CarbonImmutable
    {
        return $this->lastIncompleted;
    }

    public function lastCompleted(): ?CarbonImmutable
    {
        return $this->lastCompleted;
    }

    public function completed(): bool
    {
        if (!$this->lastCompleted()) {
            return false;
        }

        if (!$this->lastIncompleted()) {
            return $this->lastCompleted()->isToday();
        }

        return
            $this->lastCompleted()->isToday() &&
            $this->lastCompleted()->gt($this->lastIncompleted());
    }

    public function stopped(): bool
    {
        return $this->stopped;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'frequency' => $this->frequency(),
            'streak' => $this->streak(),
            'completed' => $this->completed(),
            'stopped' => $this->stopped(),
        ];
    }
}
