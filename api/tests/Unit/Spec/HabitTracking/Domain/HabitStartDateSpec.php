<?php

namespace Spec\HabitTracking\Domain;

use Carbon\CarbonImmutable;
use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\HabitStartDate;

class HabitStartDateSpec extends ObjectBehavior
{
    function it_can_be_created_from_date()
    {
        $this->beConstructedThrough('createFromDate', [
            2022, 4, 25
        ]);

        $this->shouldHaveType(CarbonImmutable::class);
        $this->shouldBeAnInstanceOf(HabitStartDate::class);

        $this->year->shouldBe(2022);
        $this->month->shouldBe(4);
        $this->day->shouldBe(25);
    }

    function it_can_be_created_from_a_DateTime_object()
    {
        $this->beConstructedWith(
            (new \DateTime)->setDate(2022, 4, 25)
        );

        $this->shouldHaveType(CarbonImmutable::class);
        $this->shouldBeAnInstanceOf(HabitStartDate::class);

        $this->year->shouldBe(2022);
        $this->month->shouldBe(4);
        $this->day->shouldBe(25);
    }
}
