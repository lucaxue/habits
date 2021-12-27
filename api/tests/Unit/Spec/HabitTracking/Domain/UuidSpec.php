<?php

namespace Spec\HabitTracking\Domain;

use Assert\InvalidArgumentException;
use HabitTracking\Domain\Uuid;
use PhpSpec\ObjectBehavior;
use Tests\Unit\Spec\CustomMatchers;

class UuidSpec extends ObjectBehavior
{
    use CustomMatchers;

    public function it_can_generate_a_uuid()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('generate');

        $this->shouldHaveType(Uuid::class);
        $this->shouldBeAnInstanceOf(ConcreteUuid::class);
        $this->toString()->shouldBeUuid();
    }

    public function it_can_be_generated_from_a_string()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', [
            $id = '25c9497d-b226-4a8d-a6df-fbebadc10b96',
        ]);

        $this->shouldHaveType(Uuid::class);
        $this->shouldBeAnInstanceOf(ConcreteUuid::class);
        $this->toString()->shouldBe($id);
    }

    public function it_guards_against_invalid_uuids()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', ['invalid uuid']);
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->duringInstantiation();
    }

    public function it_can_determine_equality()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', [
            $id = '25c9497d-b226-4a8d-a6df-fbebadc10b96',
        ]);

        $this->equals(ConcreteUuid::fromString($id))->shouldBe(true);
        $this->equals(ConcreteUuid::generate())->shouldBe(false);
        $this->equals(OtherUuid::fromString($id))->shouldBe(false);
        $this->equals(OtherUuid::generate())->shouldBe(false);
    }

    public function it_can_be_serialized()
    {
        $this->beAnInstanceOf(ConcreteUuid::class);

        $this->beConstructedThrough('fromString', [
            $id = '25c9497d-b226-4a8d-a6df-fbebadc10b96',
        ]);

        $this->shouldImplement(\JsonSerializable::class);
        $this->toString()->shouldBe($id);
        $this->__toString()->shouldBe($id);
        $this->jsonSerialize()->shouldBe($id);
    }
}

class ConcreteUuid extends Uuid
{}
class OtherUuid extends Uuid
{}
