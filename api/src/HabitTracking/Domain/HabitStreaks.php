<?php

namespace HabitTracking\Domain;

class HabitStreaks implements \JsonSerializable
{
    private ?string $lastAdded;

    public function __construct(
        private int $amount = 0,
        ?string $lastAdded = null
    ) {
        $this->lastAdded = ($amount === 0)
            ? $lastAdded
            : now()->toDateString();
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function lastAdded(): ?string
    {
        return $this->lastAdded;
    }

    public function increment(): void
    {
        if ($this->lastAdded === now()->toDateString()) {
            throw new \Exception;
        }
        $this->amount++;
        $this->lastAdded = now()->toDateString();
    }

    public function reset(): void
    {
        $this->amount = 0;
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount(),
            'last_added' => $this->lastAdded()
        ];
    }
}
