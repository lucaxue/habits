<?php

namespace Spec\HabitTracking\Domain;

use HabitTracking\Domain\HabitName;
use PhpSpec\ObjectBehavior;

class HabitNameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('Name');

        $this->shouldBeAnInstanceOf(HabitName::class);

        $this->toString()->shouldBe('Name');
    }

    function it_can_be_serialized()
    {
        $this->beConstructedWith('Name');

        $this->shouldImplement(\JsonSerializable::class);

        $this->toString()->shouldBe('Name');
        $this->__toString()->shouldBe('Name');
        $this->jsonSerialize()->shouldBe('Name');
    }
}
