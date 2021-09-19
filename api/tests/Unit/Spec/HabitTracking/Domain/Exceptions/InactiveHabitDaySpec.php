<?php

namespace Spec\HabitTracking\Domain\Exceptions;

use Carbon\Carbon;
use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\Exceptions\InactiveHabitDay;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;

class InactiveHabitDaySpec extends ObjectBehavior
{
    use InteractsWithTime;

    function it_is_initializable()
    {
        $this->beConstructedWith(HabitFrequency::weekdays());

        $this->travelTo(Carbon::createFromFormat('D', 'Sat'));

        $this->shouldHaveType(\Exception::class);
        $this->shouldBeAnInstanceOf(InactiveHabitDay::class);
        $this->getMessage()->shouldBe(
            'Sat is not included in these active days: '.
            'Mon, Tue, Wed, Thu, Fri.'
        );
    }
}
