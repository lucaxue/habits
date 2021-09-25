<?php

namespace Spec\HabitTracking\Domain;

use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitFrequency;

class HabitSpec extends ObjectBehavior
{
    function it_can_be_started()
    {
        $this->beConstructedThrough('start', [
            $id = HabitId::generate(),
            $name = 'Read a book',
            $frequency = new HabitFrequency('daily'),
        ]);

        $this->shouldBeAnInstanceOf(Habit::class);

        $this->id()->shouldBe($id);
        $this->name()->shouldBe($name);
        $this->frequency()->shouldBe($frequency);
        $this->streak()->isEmpty()->shouldBe(true);
    }

    function it_can_be_marked_as_complete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->markAsComplete();

        $this->completed()->shouldBe(true);
        $this->streak()->days()->shouldBe(1);
    }

    function it_can_be_marked_as_incomplete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->markAsComplete();
        $this->markAsIncomplete();

        $this->completed()->shouldBe(false);
        $this->streak()->days()->shouldBe(0);
    }

    function it_cannot_mark_a_completed_habit_as_complete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->markAsComplete();
        $this->shouldThrow(\Exception::class)
            ->during('markAsComplete');
    }

    function it_cannot_mark_an_incompleted_task_as_incomplete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->shouldThrow(\Exception::class)
            ->during('markAsIncomplete');
    }

    function it_can_be_edited()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->edit([
            'name' => 'Read two books',
            'frequency' => $frequency = new HabitFrequency('weekly', [1])
        ]);

        $this->name()->shouldBe('Read two books');
        $this->frequency()->shouldBe($frequency);
    }

    function it_requires_the_name_and_frequency_to_be_edited()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringEdit(['name' => 'Read two books']);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringEdit(['frequency' => new HabitFrequency('weekly', [1])]);
    }

    function it_requires_the_name_to_be_a_string()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $notStrings = [1, null, 2.5, new \stdClass(), []];

        foreach ($notStrings as $notString) {
            $this->shouldThrow(\InvalidArgumentException::class)
                ->duringEdit([
                    'name' => $notString,
                    'frequency' => new HabitFrequency('weekly', [1])
                ]);
        }
    }

    function it_requires_the_frequency_to_be_an_instance_of_HabitFrequency()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $notFrequencies = [1, null, 2.5, new \stdClass(), []];

        foreach ($notFrequencies as $notFrequency) {
            $this->shouldThrow(\InvalidArgumentException::class)
                ->duringEdit([
                    'name' => 'Read two books',
                    'frequency' => $notFrequency,
                ]);
        }
    }
}
