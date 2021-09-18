<?php

namespace Spec\HabitTracking\Domain;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Uuid;
use Tests\Unit\Spec\CustomMatchers;

class UuidSpec extends ObjectBehavior
{
    use CustomMatchers;

    function it_can_generate_a_uuid()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('generate');

        $this->shouldHaveType(Uuid::class);
        $this->shouldBeAnInstanceOf(ConcreteUuid::class);
        $this->toString()->shouldBeUuid();
    }

    function it_can_be_generated_from_a_string()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', [
            $id = '25c9497d-b226-4a8d-a6df-fbebadc10b96'
        ]);

        $this->shouldHaveType(Uuid::class);
        $this->shouldBeAnInstanceOf(ConcreteUuid::class);
        $this->toString()->shouldBe($id);
    }

    function it_can_determine_equality()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', [
            $id = '25c9497d-b226-4a8d-a6df-fbebadc10b96'
        ]);

        $this->equals(ConcreteUuid::fromString($id))->shouldBe(true);
        $this->equals(ConcreteUuid::generate())->shouldBe(false);
        $this->equals(OtherUuid::fromString($id))->shouldBe(false);
        $this->equals(OtherUuid::generate())->shouldBe(false);
    }

    function it_can_be_serialized()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', [
            $id = '25c9497d-b226-4a8d-a6df-fbebadc10b96'
        ]);

        $this->shouldImplement(\JsonSerializable::class);
        $this->toString()->shouldBe($id);
        $this->__toString()->shouldBe($id);
        $this->jsonSerialize()->shouldBe($id);
    }
}

class ConcreteUuid extends Uuid {}
class OtherUuid extends Uuid {}
