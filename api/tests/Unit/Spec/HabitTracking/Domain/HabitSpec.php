<?php

namespace Spec\HabitTracking\Domain;

use Carbon\CarbonImmutable;
use PhpSpec\ObjectBehavior;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;

class HabitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(
            $id = HabitId::generate(),
            $name = 'Read a book',
            $frequency = new HabitFrequency('daily'),
            $streak = new HabitStreak,
            $stopped = true,
            $lastCompleted = CarbonImmutable::now(),
            $lastIncompleted = CarbonImmutable::now()->subDay(),
        );

        $this->shouldBeAnInstanceOf(Habit::class);

        $this->id()->shouldBe($id);
        $this->name()->shouldBe($name);
        $this->frequency()->shouldBe($frequency);
        $this->streak()->shouldBe($streak);
        $this->stopped()->shouldBe($stopped);
        $this->lastCompleted()->shouldBe($lastCompleted);
        $this->lastIncompleted()->shouldBe($lastIncompleted);
        $this->completed()->shouldBe(true);
    }

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
        $this->lastCompleted()->shouldBe(null);
        $this->lastIncompleted()->shouldBe(null);
        $this->completed()->shouldBe(false);
        $this->stopped()->shouldBe(false);
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

        $this->edit(
            'Read two books',
            $frequency = new HabitFrequency('weekly', [1])
        );

        $this->name()->shouldBe('Read two books');
        $this->frequency()->shouldBe($frequency);
    }

    function it_can_be_stopped()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->stop();
        $this->stopped()->shouldBe(true);
    }

    function it_cannot_stop_an_already_stopped_habit()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->stop();

        $this->shouldThrow(\Exception::class)
            ->duringStop();
    }

    function it_can_be_serialized()
    {
        $this->beConstructedWith(
            $id = HabitId::generate(),
            $name = 'Read a book',
            $frequency = new HabitFrequency('daily'),
            $streak = new HabitStreak,
        );

        $this->shouldImplement(\JsonSerializable::class);

        $this->jsonSerialize()->shouldBe([
            'id' => $id,
            'name' => 'Read a book',
            'frequency' => $frequency,
            'streak' => $streak,
            'completed' => false,
            'stopped' => false,
        ]);
    }
}
