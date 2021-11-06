<?php

namespace HabitTracking\Domain;

class HabitStreak implements \JsonSerializable
{
    private int $years;
    private int $months;
    private int $days;

    public function __construct(
        int $years = 0,
        int $months = 0,
        int $days = 0
    ) {

        if ($days >= 30 || $months >= 12) {
            throw new \InvalidArgumentException();
        }

        $this->years = $years;
        $this->months = $months;
        $this->days = $days;
    }

    public static function fromString(string $streak) : self
    {
        if ( ! preg_match('/P(\d+)Y(\d+)M(\d+)D/',$streak,$matches)) {
            throw new \InvalidArgumentException();
        }

        [, $years, $months, $days] = $matches;

        return new self(...compact('years', 'months', 'days'));
    }

    public function years() : int
    {
        return $this->years;
    }

    public function months() : int
    {
        return $this->months;
    }

    public function days() : int
    {
        return $this->days;
    }

    public function isEmpty() : bool
    {
        return
            $this->years() === 0 &&
            $this->months() === 0 &&
            $this->days() === 0;
    }

    public function increment() : void
    {
        if ($this->isEndOfYear()) {
            $this->days = $this->months = 0;
            $this->years++;
            return;
        }

        if ($this->isEndOfMonth()) {
            $this->days = 0;
            $this->months++;
            return;
        }

        $this->days++;
    }

    public function decrement() : void
    {
        if ($this->isEmpty()) {
            throw new \Exception("Cannot decrement {$this->toString()}");
        }

        if ($this->isStartOfYear()) {
            $this->days = 29;
            $this->months = 11;
            $this->years--;
            return;
        }

        if ($this->isStartOfMonth()) {
            $this->days = 29;
            $this->months--;
            return;
        }

        $this->days--;
    }

    public function toString() : string
    {
        return "P{$this->years}Y{$this->months}M{$this->days}D";
    }

    public function __toString() : string
    {
        return $this->toString();
    }

    public function jsonSerialize() : string
    {
        return $this->toString();
    }

    private function isStartOfYear() : bool
    {
        return
            ! $this->isEmpty() &&
            $this->isStartOfMonth() &&
            $this->months() === 0;
    }

    private function isStartOfMonth()
    {
        return
            ! $this->isEmpty() &&
            $this->days() === 0;
    }

    private function isEndOfYear() : bool
    {
        return
            $this->isEndOfMonth() &&
            $this->months() === 11;
    }

    private function isEndOfMonth() : bool
    {
        return $this->days() === 29;
    }
}
