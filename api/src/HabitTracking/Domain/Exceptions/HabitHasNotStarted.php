<?php

namespace HabitTracking\Domain\Exceptions;

use HabitTracking\Domain\HabitStartDate;

class HabitHasNotStarted extends \Exception
{
    public function __construct(HabitStartDate $startDate)
    {
        parent::__construct(
            'Expected current date of '.now()->toDateString()
            ." to be greater than or equal to {$startDate->toDateString()}."
        );
    }
}
