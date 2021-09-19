<?php

namespace Spec\HabitTracking\Domain\Exceptions;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Exceptions\HabitStreaksIncrementedConsecutively;

class HabitStreaksIncrementedConsecutivelySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Exception::class);
        $this->shouldBeAnInstanceOf(HabitStreaksIncrementedConsecutively::class);
    }
}
