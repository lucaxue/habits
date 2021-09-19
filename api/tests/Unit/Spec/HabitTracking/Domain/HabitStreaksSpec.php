<?php

namespace Spec\HabitTracking\Domain;

use Carbon\Carbon;
use PhpSpec\ObjectBehavior;
use Tests\Unit\Spec\CustomMatchers;
use HabitTracking\Domain\HabitStreaks;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use HabitTracking\Domain\Exceptions\HabitStreaksResettedConsecutively;
use HabitTracking\Domain\Exceptions\HabitStreaksIncrementedConsecutively;

class HabitStreaksSpec extends ObjectBehavior
{
    use InteractsWithTime;
    use CustomMatchers;

    function letGo()
    {
        // Restore the time after each scenario.
        $this->travelBack();
    }

    function it_is_initialized_with_zero()
    {
        $this->shouldBeAnInstanceOf(HabitStreaks::class);

        $this->amount()->shouldBe(0);
        $this->lastAdded()->shouldBe(null);
        $this->lastResetted()->shouldBe(null);
    }

    function it_can_be_initialized_with_an_amount()
    {
        $this->travelTo($then = now());
        $this->beConstructedWith(5);

        $this->shouldBeAnInstanceOf(HabitStreaks::class);
        $this->amount()->shouldBe(5);
        $this->lastAdded()->shouldHaveHappened($then);
        $this->lastResetted()->shouldBe(null);
    }

    function it_can_increment_the_amount()
    {
        $this->travelTo($then = now());
        $this->increment();

        $this->amount()->shouldBe(1);
        $this->lastAdded()->shouldHaveHappened($then);
        $this->lastResetted()->shouldBe(null);
    }

    // function it_can_increment_the_amount_on_consecutive_days()
    // {
    //     $this->travelTo($yesterday = now()->subDay());
    //     $this->increment();

    //     $this->travelTo($today = now());
    //     $this->increment();


    //     $this->travelTo($tomorrow = now()->addDay());
    //     $this->increment();

    //     $this->amount()->shouldBe(3);
    // }

    function it_cannot_increment_the_amount_consecutively_during_same_day()
    {
        $this->increment();
        $this->shouldThrow(HabitStreaksIncrementedConsecutively::class)->during('increment');
    }

    function it_can_reset_the_amount()
    {
        $this->travelTo($yesterday = now()->subDay());
        $this->beConstructedWith(5);

        $this->travelTo($then = now());
        $this->reset();

        $this->amount()->shouldBe(0);
        $this->lastAdded()->shouldHaveHappened($yesterday);
        $this->lastResetted()->shouldHaveHappened($then);
    }

    // function it_can_reset_the_amount_on_consecutive_days()
    // {
    //     $this->travelTo($yesterday = now()->subDay());
    //     $this->reset();

    //     $this->travelTo($today = now());
    //     $this->reset();

    //     $this->travelTo($tomorrow = now()->addDay());
    //     $this->reset();

    //     $this->amount()->shouldBe(0);
    // }

    function it_cannot_reset_the_amount_consecutively_during_same_day()
    {
        $this->reset();
        $this->shouldThrow(HabitStreaksResettedConsecutively::class)->during('reset');
    }

    function it_can_toggle_between_reset_and_increment()
    {
        $this->travelTo($then = now());

        $this->reset();
        $this->increment();
        $this->reset();
        $this->increment();

        $this->amount()->shouldBe(1);
        $this->lastAdded()->shouldHaveHappened($then);
        $this->lastResetted()->shouldHaveHappened($then);
    }

    function it_can_be_serialized()
    {
        $this->beConstructedWith(
            $amount = 0,
            $lastAdded = new Carbon,
            $lastResetted = new Carbon,
        );

        $this->shouldImplement(\JsonSerializable::class);
        $this->jsonSerialize()->shouldBe([
            'amount' => $amount,
            'last_added' => $lastAdded,
            'last_resetted' => $lastResetted,
        ]);
    }
}
