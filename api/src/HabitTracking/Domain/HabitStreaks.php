<?php

namespace HabitTracking\Domain;

use Carbon\Carbon;
use HabitTracking\Domain\Exceptions\HabitStreaksResettedConsecutively;
use HabitTracking\Domain\Exceptions\HabitStreaksIncrementedConsecutively;

class HabitStreaks implements \JsonSerializable
{
    private ?Carbon $lastAdded;

    public function __construct(
        private int $amount = 0,
        ?Carbon $lastAdded = null,
        private ?Carbon $lastResetted = null,
    ) {
        $this->lastAdded = ($amount === 0)
            ? $lastAdded
            : now();
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function lastAdded(): ?Carbon
    {
        return $this->lastAdded;
    }

    public function lastResetted(): ?Carbon
    {
        return $this->lastResetted;
    }

    /** @throws HabitStreaksIncrementedConsecutively */
    public function increment(): void
    {
        if (
            $this->lastAdded() &&
            $this->lastAdded()->isToday() &&
            $this->lastAdded()->gt($this->lastResetted())
        ) {
            throw new HabitStreaksIncrementedConsecutively;
        }

        $this->amount++;
        $this->lastAdded = now();
    }

    /** @throws HabitStreaksResettedConsecutively */
    public function reset(): void
    {
        if (
            $this->lastResetted() &&
            $this->lastResetted()->isToday() &&
            $this->lastResetted()->gt($this->lastAdded())
        ) {
            throw new HabitStreaksResettedConsecutively;
        }

        $this->amount = 0;
        $this->lastResetted = now();
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount(),
            'last_added' => $this->lastAdded(),
            'last_resetted' => $this->lastResetted()
        ];
    }
}
