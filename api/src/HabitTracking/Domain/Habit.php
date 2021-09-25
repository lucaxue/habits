<?php

namespace HabitTracking\Domain;

use Carbon\CarbonImmutable;
use HabitTracking\Domain\HabitId;

class Habit implements \JsonSerializable
{
    private ?CarbonImmutable $lastCompleted = null;
    private ?CarbonImmutable $lastIncompleted = null;
    private HabitStreak $streak;

    private function __construct(
        private HabitId $id,
        private string $name,
        private HabitFrequency $frequency,
        ?HabitStreak $streak = null,
    ) {

        $this->streak = $streak ?? new HabitStreak;
    }

    public static function start(
        HabitId $id,
        string $name,
        HabitFrequency $frequency
    ): self {

        return new self($id, $name, $frequency);
    }

    public function markAsComplete(): void
    {
        if ($this->completed()) {
            throw new \Exception;
        }

        $this->lastCompleted = new CarbonImmutable;
        $this->streak()->increment();
    }

    public function markAsIncomplete(): void
    {
        if (!$this->completed()) {
            throw new \Exception;
        }

        $this->lastIncompleted = new CarbonImmutable;
        $this->streak()->decrement();
    }

    public function completed(): bool
    {
        if (!$this->lastCompleted) {
            return false;
        }

        if (!$this->lastIncompleted) {
            return $this->lastCompleted->isToday();
        }

        return
            $this->lastCompleted->isToday() &&
            $this->lastCompleted->gt($this->lastIncompleted);
    }

    public function id(): HabitId
    {
        return $this->id;
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

    public function edit(array $payload): void
    {
        if (
            !array_key_exists('name', $payload) ||
            !array_key_exists('frequency', $payload) ||
            !is_string($payload['name']) ||
            !($payload['frequency'] instanceof HabitFrequency)
        ) {
            throw new \InvalidArgumentException;
        }

        $this->name = $payload['name'];
        $this->frequency = $payload['frequency'];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'frequency' => $this->frequency(),
            'streak' => $this->streak(),
            'completed' => $this->completed(),
        ];
    }
}
