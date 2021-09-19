<?php

namespace Spec\HabitTracking\Domain\Exceptions;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Exceptions\HabitStreaksResettedConsecutively;

class HabitStreaksResettedConsecutivelySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Exception::class);
        $this->shouldBeAnInstanceOf(HabitStreaksResettedConsecutively::class);
    }
}
