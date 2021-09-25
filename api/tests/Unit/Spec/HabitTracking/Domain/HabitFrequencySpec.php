<?php

namespace Spec\HabitTracking\Domain;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\HabitFrequency;

class HabitFrequencySpec extends ObjectBehavior
{
    function it_can_be_initialized_with_daily()
    {
        $this->beConstructedWith('daily');

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->type()->shouldBe('daily');
        $this->days()->shouldBe(null);
    }

    function it_can_be_initialized_with_weekly()
    {
        $this->beConstructedWith('weekly', [1, 2, 3]);

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->type()->shouldBe('weekly');
        $this->days()->shouldBe([1, 2, 3]);
    }

    function it_requires_days_when_initializing_with_weekly()
    {
        $this->beConstructedWith('weekly');

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();
    }

    function it_can_be_serialized()
    {
        $this->beConstructedWith('weekly', [1, 2, 3]);

        $this->shouldImplement(\JsonSerializable::class);
        $this->jsonSerialize()->shouldBe([
            'type' => 'weekly',
            'days' => [1, 2, 3],
        ]);
    }
}
