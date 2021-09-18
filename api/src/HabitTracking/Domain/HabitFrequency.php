<?php

namespace HabitTracking\Domain;

use JsonSerializable;

class HabitFrequency implements JsonSerializable
{
    public function __construct(
        private bool $mon,
        private bool $tue,
        private bool $wed,
        private bool $thu,
        private bool $fri,
        private bool $sat,
        private bool $sun,
    ) {
    }

    public static function fromInactiveDays(
        array $inactiveDays
    ): self {

        $inactiveDays = collect($inactiveDays)
            ->mapWithKeys(fn ($day) => [$day => false])
            ->all();

        return new self(...array_merge([
            'mon' => true,
            'tue' => true,
            'wed' => true,
            'thu' => true,
            'fri' => true,
            'sat' => true,
            'sun' => true,
        ], $inactiveDays));
    }

    public static function fromActiveDays(
        array $activeDays
    ): self {

        $activeDays = collect($activeDays)
            ->mapWithKeys(fn ($day) => [$day => true])
            ->all();

        return new self(...array_merge([
            'mon' => false,
            'tue' => false,
            'wed' => false,
            'thu' => false,
            'fri' => false,
            'sat' => false,
            'sun' => false,
        ], $activeDays));
    }

    public function activeDays(): array
    {
        $activeDays = [];

        foreach ($this as $day => $isActive) {
            if ($isActive) $activeDays[] = $day;
        }

        return $activeDays;
    }

    public function inactiveDays(): array
    {
        $inactiveDays = [];

        foreach ($this as $day => $isActive) {
            if (!$isActive) $inactiveDays[] = $day;
        }

        return $inactiveDays;
    }

    public function jsonSerialize(): array
    {
        return [
            'activeDays' => $this->activeDays(),
            'inactiveDays' => $this->inactiveDays(),
        ];
    }
}
