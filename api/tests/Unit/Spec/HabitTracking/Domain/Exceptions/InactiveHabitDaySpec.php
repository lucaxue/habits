<?php

namespace Spec\HabitTracking\Domain\Exceptions;

use HabitTracking\Domain\Exceptions\InactiveHabitDay;
use PhpSpec\ObjectBehavior;

class InactiveHabitDaySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InactiveHabitDay::class);
    }
}
