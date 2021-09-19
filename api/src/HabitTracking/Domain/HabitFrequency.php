<?php

namespace HabitTracking\Domain;

class HabitFrequency implements \JsonSerializable
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

    public static function weekends(): self
    {
        return new self(...[
            'mon' => false,
            'tue' => false,
            'wed' => false,
            'thu' => false,
            'fri' => false,
            'sat' => true,
            'sun' => true
        ]);
    }

    public static function weekdays(): self
    {
        return new self(...[
            'mon' => true,
            'tue' => true,
            'wed' => true,
            'thu' => true,
            'fri' => true,
            'sat' => false,
            'sun' => false
        ]);
    }

    public static function daily(): self
    {
        return new self(...[
            'mon' => true,
            'tue' => true,
            'wed' => true,
            'thu' => true,
            'fri' => true,
            'sat' => true,
            'sun' => true
        ]);
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

    public function hasTodayAsActive(): bool
    {
        return in_array(
            strtolower(now()->format('D')),
            $this->activeDays()
        );
    }

    public function hasTodayAsInactive(): bool
    {
        return ! $this->hasTodayAsActive();
    }
}
