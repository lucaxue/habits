<?php

namespace Spec\HabitTracking\Domain;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\HabitStreaks;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;

class HabitStreaksSpec extends ObjectBehavior
{
    use InteractsWithTime;

    function it_is_initialized_with_zero()
    {
        $this->shouldBeAnInstanceOf(HabitStreaks::class);

        $this->amount()->shouldBe(0);
        $this->lastAdded()->shouldBe(null);
    }

    function it_can_be_initialized_with_an_amount()
    {
        $this->beConstructedWith(5);

        $this->shouldBeAnInstanceOf(HabitStreaks::class);
        $this->amount()->shouldBe(5);
        $this->lastAdded()->shouldBe(now()->toDateString());
    }

    function it_can_increment_the_amount()
    {
        $this->increment();

        $this->amount()->shouldBe(1);
        $this->lastAdded()->shouldBe(now()->toDateString());
    }

    function it_cannot_increment_more_than_once_the_same_day()
    {
        $this->increment();

        $this->shouldThrow(\Exception::class)->during('increment');
    }

    function it_can_be_resetted()
    {
        $this->travelTo($yesterday = now()->subDay());

        $this->beConstructedWith(5);
        $this->lastAdded()->shouldBe(now()->toDateString());

        $this->travelBack();

        $this->reset();
        $this->amount()->shouldBe(0);
        $this->lastAdded()->shouldBe($yesterday->toDateString());
    }

    function it_can_be_serialized()
    {
        $this->increment();

        $this->shouldImplement(\JsonSerializable::class);
        $this->jsonSerialize()->shouldBe([
            'amount' => 1,
            'last_added' => now()->toDateString()
        ]);
    }
}
