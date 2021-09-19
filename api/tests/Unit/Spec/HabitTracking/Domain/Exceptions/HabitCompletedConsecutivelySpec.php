<?php

namespace Spec\HabitTracking\Domain\Exceptions;

use HabitTracking\Domain\Exceptions\HabitCompletedConsecutively;
use PhpSpec\ObjectBehavior;

class HabitCompletedConsecutivelySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Exception::class);
        $this->shouldBeAnInstanceOf(HabitCompletedConsecutively::class);
    }
}
