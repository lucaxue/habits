<?php

namespace Spec\HabitTracking\Domain;

use HabitTracking\Domain\HabitStreak;
use PhpSpec\ObjectBehavior;

class HabitStreakSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(HabitStreak::class);

        $this->isEmpty()->shouldBe(true);
        $this->years()->shouldBe(0);
        $this->months()->shouldBe(0);
        $this->days()->shouldBe(0);
    }

    public function it_can_be_initialized_with_years_months_and_days()
    {
        $this->beConstructedWith(1, 2, 3);

        $this->shouldBeAnInstanceOf(HabitStreak::class);

        $this->isEmpty()->shouldBe(false);
        $this->years()->shouldBe(1);
        $this->months()->shouldBe(2);
        $this->days()->shouldBe(3);
    }

    public function it_can_be_initialized_from_string()
    {
        $this->beConstructedThrough('fromString', ['P1Y2M3D']);

        $this->shouldBeAnInstanceOf(HabitStreak::class);

        $this->isEmpty()->shouldBe(false);
        $this->years()->shouldBe(1);
        $this->months()->shouldBe(2);
        $this->days()->shouldBe(3);
    }

    public function it_guards_against_invalid_string_formats()
    {
        $invalidFormats = [
            '01:02:03', 'Y1M2D3', 'PY1M2D3', 'PP1Y2M3DD',
            'PPYYMMDD', 'P0.1Y5.3M2.3D', '010203',
            '1 Year 2 Months 3 Days',
        ];

        foreach ($invalidFormats as $invalidFormat) {
            $this->beConstructedThrough('fromString', [$invalidFormat]);
            $this->shouldThrow(\InvalidArgumentException::class)
                ->duringInstantiation();
        }
    }

    public function it_guards_against_invalid_days()
    {
        $this->beConstructedWith(0, 0, 31);
        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();

        $this->beConstructedWith(0, 0, 30);
        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();

        $this->beConstructedWith(0, 0, 29);
        $this->days()->shouldBe(29);
    }

    public function it_guards_against_invalid_months()
    {
        $this->beConstructedWith(0, 13, 0);
        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();

        $this->beConstructedWith(0, 12, 0);
        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();

        $this->beConstructedWith(0, 11, 0);
        $this->months()->shouldBe(11);
    }

    public function it_can_be_incremented_by_a_day()
    {
        $this->beConstructedWith(0, 0, 0);

        $incremented = $this->increment();

        $incremented->years()->shouldBe(0);
        $incremented->months()->shouldBe(0);
        $incremented->days()->shouldBe(1);
    }

    public function it_can_be_decremented_a_day()
    {
        $this->beConstructedWith(0, 0, 2);

        $decremented = $this->decrement();

        $decremented->years()->shouldBe(0);
        $decremented->months()->shouldBe(0);
        $decremented->days()->shouldBe(1);
    }

    public function it_cannot_decrement_an_empty_streak()
    {
        $this->beConstructedWith(0, 0, 0);

        $this->shouldThrow(\Exception::class)
            ->during('decrement');
    }

    public function it_considers_30_days_to_be_a_month()
    {
        $this->beConstructedWith(0, 0, 29);

        $incremented = $this->increment();

        $incremented->months()->shouldBe(1);
        $incremented->days()->shouldBe(0);

        $decremented = $incremented->decrement();

        $decremented->months()->shouldBe(0);
        $decremented->days()->shouldBe(29);
    }

    public function it_considers_12_months_to_be_a_year()
    {
        $this->beConstructedWith(0, 11, 29);

        $incremented = $this->increment();

        $incremented->years()->shouldBe(1);
        $incremented->months()->shouldBe(0);
        $incremented->days()->shouldBe(0);

        $decremented = $incremented->decrement();

        $decremented->years()->shouldBe(0);
        $decremented->months()->shouldBe(11);
        $decremented->days()->shouldBe(29);
    }

    public function it_can_be_serialized()
    {
        $this->beConstructedThrough('fromString', ['P1Y2M3D']);

        $this->shouldImplement(\Stringable::class);
        $this->__toString()->shouldBe('P1Y2M3D');

        $this->shouldImplement(\JsonSerializable::class);
        $this->jsonSerialize()->shouldBe('P1Y2M3D');
    }
}
