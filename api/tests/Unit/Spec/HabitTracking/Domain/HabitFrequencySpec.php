<?php

namespace Spec\HabitTracking\Domain;

use Carbon\Carbon;
use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\HabitFrequency;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;

class HabitFrequencySpec extends ObjectBehavior
{
    use InteractsWithTime;

    function it_can_be_initialized()
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

    function it_can_be_initialized_for_daily()
    {
        $this->beConstructedThrough('daily');

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->activeDays()->shouldBe([
            'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'
        ]);
        $this->inactiveDays()->shouldBe([]);
    }

    function it_can_be_initialized_for_weekdays()
    {
        $this->beConstructedThrough('weekdays');

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->activeDays()->shouldBe(['mon', 'tue', 'wed', 'thu', 'fri']);
        $this->inactiveDays()->shouldBe(['sat', 'sun']);
    }

    function it_can_be_initialized_for_weekends()
    {
        $this->beConstructedThrough('weekends');

        $this->shouldBeAnInstanceOf(HabitFrequency::class);

        $this->activeDays()->shouldBe(['sat', 'sun']);
        $this->inactiveDays()->shouldBe(['mon', 'tue', 'wed', 'thu', 'fri']);
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

    function it_can_determine_if_today_is_active_or_not()
    {
        $this->beConstructedThrough('weekdays');

        $this->travelTo(Carbon::createFromFormat('D', 'Mon'));
        $this->hasTodayAsActive()->shouldBe(true);
        $this->hasTodayAsInactive()->shouldBe(false);

        $this->travelTo(Carbon::createFromFormat('D', 'Sat'));
        $this->hasTodayAsActive()->shouldBe(false);
        $this->hasTodayAsInactive()->shouldBe(true);
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
