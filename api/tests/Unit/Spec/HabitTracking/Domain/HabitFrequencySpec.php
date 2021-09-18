<?php

namespace Spec\HabitTracking\Domain;

use HabitTracking\Domain\HabitFrequency;
use PhpSpec\ObjectBehavior;

class HabitFrequencySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        // Omit `array_values` for actual usage,
        // see php 8 named arguments:
        // https://stitcher.io/blog/php-8-named-arguments
        $this->beConstructedWith(...array_values([
            'mon' => true,
            'tue' => true,
            'wed' => true,
            'thu' => true,
            'fri' => true,
            'sat' => false,
            'sun' => false,
        ]));

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->activeDays()->shouldBe(['mon', 'tue', 'wed', 'thu', 'fri']);
        $this->inactiveDays()->shouldBe(['sat', 'sun']);
    }

    function it_can_be_initialized_from_active_days()
    {
        $this->beConstructedThrough('fromActiveDays', [
            ['mon', 'tue', 'wed', 'thu', 'fri']
        ]);

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->activeDays()->shouldBe(['mon', 'tue', 'wed', 'thu', 'fri']);
        $this->inactiveDays()->shouldBe(['sat', 'sun']);
    }

    function it_can_be_initialized_from_inactive_days()
    {
        $this->beConstructedThrough('fromInactiveDays', [
            ['sat', 'sun']
        ]);

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->activeDays()->shouldBe(['mon', 'tue', 'wed', 'thu', 'fri']);
        $this->inactiveDays()->shouldBe(['sat', 'sun']);
    }

    function it_can_be_serialized()
    {
        $frequency = [
            'mon' => true,
            'tue' => true,
            'wed' => true,
            'thu' => true,
            'fri' => true,
            'sat' => false,
            'sun' => false,
        ];

        $this->beConstructedWith(...array_values($frequency));

        $this->shouldImplement(\JsonSerializable::class);
        $this->jsonSerialize()->shouldBe([
            'activeDays' => ['mon', 'tue', 'wed', 'thu', 'fri'],
            'inactiveDays' => ['sat', 'sun']
        ]);
    }
}
