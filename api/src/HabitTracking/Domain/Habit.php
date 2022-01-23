<?php

namespace HabitTracking\Domain;

use Carbon\CarbonImmutable;
use HabitTracking\Domain\Exceptions\HabitAlreadyCompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyIncompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyStopped;

class Habit implements \JsonSerializable
{
    public function __construct(
        private HabitId $id,
        private int $authorId,
        private string $name,
        private HabitFrequency $frequency,
        private ? HabitStreak $streak = null,
        private bool $stopped = false,
        private ? CarbonImmutable $lastCompleted = null,
    ) {
        $this->streak ??= new HabitStreak();
    }

    public static function start(
        HabitId $id,
        int $authorId,
        string $name,
        HabitFrequency $frequency
    ) : self {

        return new self($id, $authorId, $name, $frequency);
    }

    public function markAsComplete() : void
    {
        if ($this->completed()) {
            throw new HabitAlreadyCompleted();
        }

        $this->streak = $this->streak()->increment();
        $this->lastCompleted = CarbonImmutable::now();
    }

    public function markAsIncomplete() : void
    {
        if ( ! $this->completed()) {
            throw new HabitAlreadyIncompleted();
        }

        $this->streak = $this->streak()->decrement();
        $this->lastCompleted = null;
    }

    public function edit(
        string $name,
        HabitFrequency $frequency
    ) : void {

        $this->name = $name;
        $this->frequency = $frequency;
    }

    public function reset() : void
    {
        if ( ! $this->completed()) {
            $this->streak = new HabitStreak();
        }

        $this->lastCompleted = null;
    }

    public function stop() : void
    {
        if ($this->stopped()) {
            throw new HabitAlreadyStopped();
        }

        $this->stopped = true;
    }

    public function id() : HabitId
    {
        return $this->id;
    }

    public function authorId() : int
    {
        return $this->authorId;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function frequency() : HabitFrequency
    {
        return $this->frequency;
    }

    public function streak() : HabitStreak
    {
        return $this->streak;
    }

    public function lastCompleted() : ? CarbonImmutable
    {
        return $this->lastCompleted;
    }

    public function completed() : bool
    {
        return null !== $this->lastCompleted();
    }

    public function stopped() : bool
    {
        return $this->stopped;
    }

    public function jsonSerialize() : array
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
