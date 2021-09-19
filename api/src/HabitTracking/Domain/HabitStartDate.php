<?php

namespace HabitTracking\Domain;

use Carbon\CarbonImmutable;

class HabitStartDate extends CarbonImmutable
{
    public function jsonSerialize(): string
    {
        return $this->toDateString();
    }

    public function equals(HabitStartDate $candidate): bool
    {
        return $this->toDateString() === $candidate->toDateString();
    }
}
