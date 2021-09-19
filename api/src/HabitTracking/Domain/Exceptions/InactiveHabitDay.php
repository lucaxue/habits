<?php

namespace HabitTracking\Domain\Exceptions;

use HabitTracking\Domain\HabitFrequency;

class InactiveHabitDay extends \Exception
{
    public function __construct(HabitFrequency $frequency)
    {
        $today = now()->format('D');

        $activeDays = array_map(
            fn ($day) => ucfirst($day),
            $frequency->activeDays()
        );

        parent::__construct(
            "{$today} is not included in these active days: ".
            implode(', ', $activeDays).'.'
        );
    }
}
