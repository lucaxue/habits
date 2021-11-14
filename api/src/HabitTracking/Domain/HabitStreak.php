<?php

namespace HabitTracking\Domain;

use Assert\Assertion;

class HabitStreak implements \JsonSerializable
{
    public function __construct(
        private int $years = 0,
        private int $months = 0,
        private int $days = 0,
    ) {
        Assertion::lessThan($days, 30);
        Assertion::lessThan($months, 12);
    }

    public static function fromString(string $streak) : self
    {
        $pattern = '/^P(\d+)Y(\d+)M(\d+)D$/';

        if ( ! preg_match($pattern, $streak, $matches)) {
            throw new \InvalidArgumentException();
        }

        [, $years, $months, $days] = $matches;

        return new self($years, $months, $days);
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

    public function increment() : self
    {
        if ($this->isEndOfYear()) {
            return new self($this->years() + 1, 0, 0);
        }

        if ($this->isEndOfMonth()) {
            return new self($this->years(), $this->months() + 1, 0);
        }

        return new self(
            $this->years(),
            $this->months(),
            $this->days() + 1,
        );
    }

    public function decrement() : self
    {
        if ($this->isEmpty()) {
            throw new \Exception("Cannot decrement {$this->toString()}");
        }

        if ($this->isStartOfYear()) {
            return new self($this->years() - 1, 11, 29);
        }

        if ($this->isStartOfMonth()) {
            return new self($this->years(), $this->months() - 1, 29);
        }

        return new self(
            $this->years(),
            $this->months(),
            $this->days() - 1
        );
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

    private function isStartOfMonth() : bool
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
