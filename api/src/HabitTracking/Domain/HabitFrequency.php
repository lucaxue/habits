<?php

namespace HabitTracking\Domain;

use Carbon\CarbonImmutable;

class HabitFrequency implements \JsonSerializable
{
    public const SUNDAY = CarbonImmutable::SUNDAY;
    public const MONDAY = CarbonImmutable::MONDAY;
    public const TUESDAY = CarbonImmutable::TUESDAY;
    public const WEDNESDAY = CarbonImmutable::WEDNESDAY;
    public const THURSDAY = CarbonImmutable::THURSDAY;
    public const FRIDAY = CarbonImmutable::FRIDAY;
    public const SATURDAY = CarbonImmutable::SATURDAY;

    private string $type;
    private ?array $days;

    public function __construct(
        string $type,
        ?array $days = null
    ) {

        if (( ! in_array($type, ['daily', 'weekly'])) ||
            ($type === 'weekly' && ! $days)
        ) {
            throw new \InvalidArgumentException();
        }

        $this->type = $type;
        $this->days = $days;
    }

    public function type() : string
    {
        return $this->type;
    }

    public function days() : ?array
    {
        return $this->days;
    }

    public function includesToday() : bool
    {
        if ( ! $this->days()) { return true; }

        return in_array(now()->dayOfWeek, $this->days());
    }

    public function jsonSerialize() : array
    {
        return [
            'type' => $this->type(),
            'days' => $this->days(),
        ];
    }
}
