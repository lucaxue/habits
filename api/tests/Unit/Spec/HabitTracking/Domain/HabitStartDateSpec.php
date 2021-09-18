<?php

namespace Spec\HabitTracking\Domain;

use Carbon\CarbonImmutable;
use PhpSpec\ObjectBehavior;
use Tests\Unit\Spec\CustomMatchers;
use HabitTracking\Domain\HabitStartDate;

class HabitStartDateSpec extends ObjectBehavior
{
    use CustomMatchers;

    function it_can_be_created_from_date()
    {
        $this->beConstructedThrough('createFromDate', [2022, 4, 25]);

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

    function it_can_be_serialized()
    {
        $this->beConstructedThrough('createFromDate', [2022, 4, 25]);

        $this->shouldImplement(\JsonSerializable::class);

        $this->jsonSerialize()->shouldBe('2022-04-25');
        $this->jsonSerialize()->shouldHaveDateFormat('Y-m-d');
    }

    function it_can_determine_equality()
    {
        $this->beConstructedThrough('createFromDate', [2022, 4, 25]);

        $this->equals(
            HabitStartDate::createFromDate(2022, 4, 25)
        )->shouldBe(true);

        $this->equals(
            HabitStartDate::createFromDate(2005, 05, 20)
        )->shouldBe(false);
    }
}
