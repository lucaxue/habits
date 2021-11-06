<?php

namespace Spec\HabitTracking\Domain;

use HabitTracking\Domain\HabitFrequency;
use PhpSpec\ObjectBehavior;

class HabitFrequencySpec extends ObjectBehavior
{
    public function it_can_be_initialized_with_daily()
    {
        $this->beConstructedWith('daily');

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->type()->shouldBe('daily');
        $this->days()->shouldBe(null);
    }

    public function it_can_be_initialized_with_weekly()
    {
        $this->beConstructedWith('weekly', [
            HabitFrequency::MONDAY,
            HabitFrequency::TUESDAY,
            HabitFrequency::WEDNESDAY,
        ]);

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->type()->shouldBe('weekly');
        $this->days()->shouldBe([
            HabitFrequency::MONDAY,
            HabitFrequency::TUESDAY,
            HabitFrequency::WEDNESDAY,
        ]);
    }

    public function it_requires_days_when_initializing_with_weekly()
    {
        $this->beConstructedWith('weekly');

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();
    }

    public function it_can_determine_if_it_includes_today_when_daily()
    {
        $this->beConstructedWith('daily');

        $this->includesToday()->shouldBe(true);
    }

    public function it_can_determine_if_it_includes_today_when_weekly()
    {
        $this->beConstructedWith('weekly', [now()->dayOfWeek]);

        $this->includesToday()->shouldBe(true);
    }

    public function it_can_be_serialized()
    {
        $this->beConstructedWith('weekly', [
            HabitFrequency::MONDAY,
            HabitFrequency::TUESDAY,
            HabitFrequency::WEDNESDAY,
        ]);

        $this->shouldImplement(\JsonSerializable::class);
        $this->jsonSerialize()->shouldBe([
            'type' => 'weekly',
            'days' => [
                HabitFrequency::MONDAY,
                HabitFrequency::TUESDAY,
                HabitFrequency::WEDNESDAY,
            ],
        ]);
    }
}
