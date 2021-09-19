<?php

namespace Spec\HabitTracking\Domain;

use Carbon\Carbon;
use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitName;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitStartDate;
use HabitTracking\Domain\Exceptions\InactiveHabitDay;
use HabitTracking\Domain\Exceptions\HabitHasNotStarted;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;

class HabitSpec extends ObjectBehavior
{
    use InteractsWithTime;

    function it_can_be_planned()
    {
        $this->beConstructedThrough('plan', [
            $id = HabitId::generate(),
            $name = new HabitName('Read a book'),
            $startDate = HabitStartDate::now(),
            $frequency = HabitFrequency::daily(),
        ]);

        $this->shouldBeAnInstanceOf(Habit::class);

        $this->id()->shouldBe($id);
        $this->name()->shouldBe($name);
        $this->startDate()->shouldBe($startDate);
        $this->frequency()->shouldBe($frequency);
    }

    function it_can_be_marked_as_complete()
    {
        $this->beConstructedThrough('plan', [
            HabitId::generate(),
            new HabitName('Read a book'),
            HabitStartDate::now(), // starts now
            HabitFrequency::daily(),
        ]);

        $this->markAsComplete();

        $this->streaks()->shouldBe(1);
    }

    function it_cannot_be_marked_as_complete_before_the_start_date()
    {
        $this->beConstructedThrough('plan', [
            HabitId::generate(),
            new HabitName('Read a book'),
            HabitStartDate::now()->addDay(), // starts tomorrow
            HabitFrequency::daily(),
        ]);

        $this->shouldThrow(HabitHasNotStarted::class)->during('markAsComplete');
    }

    function it_cannot_be_marked_as_complete_if_today_is_not_an_active_day()
    {
        $this->beConstructedThrough('plan', [
            HabitId::generate(),
            new HabitName('Read a book'),
            HabitStartDate::now(),
            HabitFrequency::weekdays(), // active on weekdays
        ]);

        $this->travelTo(Carbon::createFromFormat('D', 'Sat'));

        $this->shouldThrow(InactiveHabitDay::class)->during('markAsComplete');
    }
}
