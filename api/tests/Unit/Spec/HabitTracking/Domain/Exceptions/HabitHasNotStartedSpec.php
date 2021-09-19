<?php

namespace Spec\HabitTracking\Domain\Exceptions;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\HabitStartDate;
use HabitTracking\Domain\Exceptions\HabitHasNotStarted;

class HabitHasNotStartedSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(
            $startDate = HabitStartDate::now()->addDay()
        );

        $this->shouldHaveType(\Exception::class);
        $this->shouldBeAnInstanceOf(HabitHasNotStarted::class);
        $this->getMessage()->shouldBe(
            'Expected current date of '.now()->toDateString().
            " to be greater than or equal to {$startDate->toDateString()}."
        );
    }
}
