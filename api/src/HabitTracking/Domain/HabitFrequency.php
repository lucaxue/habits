<?php

namespace HabitTracking\Domain;

class HabitFrequency implements \JsonSerializable
{
    private string $type;
    private ?array $days;

    public function __construct(
        string $type,
        ?array $days = null
    ) {

        if ((! in_array($type, ['daily', 'weekly'])) ||
            ($type === 'weekly' && !$days)
        ) {
            throw new \InvalidArgumentException;
        }

        $this->type = $type;
        $this->days = $days;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function days(): ?array
    {
        return $this->days;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type(),
            'days' => $this->days(),
        ];
    }
}
