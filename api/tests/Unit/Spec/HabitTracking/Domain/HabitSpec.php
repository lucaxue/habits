<?php

namespace Spec\HabitTracking\Domain;

use Carbon\Carbon;
use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitName;
use Tests\Unit\Spec\CustomMatchers;
use HabitTracking\Domain\HabitStreaks;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitStartDate;
use HabitTracking\Domain\Exceptions\InactiveHabitDay;
use HabitTracking\Domain\Exceptions\HabitHasNotStarted;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use HabitTracking\Domain\Exceptions\HabitCompletedConsecutively;

class HabitSpec extends ObjectBehavior
{
    use InteractsWithTime;
    use CustomMatchers;

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
        $this->streaks()->amount()->shouldBe(0);
        $this->lastCompleted()->shouldBe(null);
    }

    function it_has_a_streaks_setter()
    {
        $this->beConstructedThrough('plan', [
            HabitId::generate(),
            new HabitName('Read a book'),
            HabitStartDate::now(),
            HabitFrequency::daily(),
        ]);

        $this->setStreaks($streaks = new HabitStreaks(5));
        $this->streaks()->shouldBe($streaks);
        $this->lastCompleted()->shouldBe($streaks->lastAdded()->toDateString());
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

        $this->lastCompleted()->shouldBeToday();
        $this->streaks()->amount()->shouldBe(1);
    }

    function it_cannot_be_marked_as_complete_consecutively_during_the_same_day()
    {
        $this->beConstructedThrough('plan', [
            HabitId::generate(),
            new HabitName('Read a book'),
            HabitStartDate::now(),
            HabitFrequency::daily(),
        ]);

        $this->markAsComplete();
        $this->shouldThrow(HabitCompletedConsecutively::class)->during('markAsComplete');
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

    function it_can_be_marked_as_incomplete()
    {
        $this->beConstructedThrough('plan', [
            HabitId::generate(),
            new HabitName('Read a book'),
            HabitStartDate::now()->subWeek(),
            HabitFrequency::daily(),
        ]);

        $this->travelTo($yesterday = now()->subDay());
        $this->markAsComplete();

        $this->travelBack();
        $this->markAsIncomplete();

        $this->streaks()->amount()->shouldBe(0);
        $this->lastCompleted()->shouldBe($yesterday->toDateString());
    }

    // function it_recovers_the_streaks_when_it_is_marked_back_to_complete_during_the_same_day()
    // {
    //     $this->beConstructedThrough('plan', [
    //         HabitId::generate(),
    //         new HabitName('Read a book'),
    //         HabitStartDate::now()->subWeek(),
    //         HabitFrequency::daily(),
    //     ]);
    //     $this->setStreaks(new HabitStreaks(5));

    //     $this->markAsIncomplete();
    //     $this->markAsComplete();
    //     $this->markAsIncomplete();
    //     $this->markAsComplete();

    //     $this->streaks()->amount()->shouldBe(5);
    // }
}
