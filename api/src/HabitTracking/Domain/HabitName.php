<?php

namespace HabitTracking\Domain;

class HabitName implements \JsonSerializable
{
    public function __construct(
        private string $name
    ) {
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function equals(HabitName $candidate): bool
    {
        return $this->toString() === $candidate->toString();
    }
}
