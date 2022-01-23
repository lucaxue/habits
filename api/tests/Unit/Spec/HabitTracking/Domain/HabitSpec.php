<?php

namespace Spec\HabitTracking\Domain;

use Carbon\CarbonImmutable;
use HabitTracking\Domain\Exceptions\HabitAlreadyCompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyIncompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyStopped;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use PhpSpec\ObjectBehavior;

class HabitSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(
            $id = HabitId::generate(),
            $authorId = 3,
            $name = 'Read a book',
            $frequency = new HabitFrequency('daily'),
            $streak = new HabitStreak(),
            $stopped = true,
            $lastCompleted = CarbonImmutable::now(),
        );

        $this->shouldBeAnInstanceOf(Habit::class);

        $this->id()->shouldBe($id);
        $this->authorId()->shouldBe($authorId);
        $this->name()->shouldBe($name);
        $this->frequency()->shouldBe($frequency);
        $this->streak()->shouldBe($streak);
        $this->stopped()->shouldBe($stopped);
        $this->lastCompleted()->shouldBe($lastCompleted);
        $this->completed()->shouldBe(true);
    }

    public function it_can_be_started()
    {
        $this->beConstructedThrough('start', [
            $id = HabitId::generate(),
            $authorId = 3,
            $name = 'Read a book',
            $frequency = new HabitFrequency('daily'),
        ]);

        $this->shouldBeAnInstanceOf(Habit::class);

        $this->id()->shouldBe($id);
        $this->authorId()->shouldBe($authorId);
        $this->name()->shouldBe($name);
        $this->frequency()->shouldBe($frequency);
        $this->streak()->isEmpty()->shouldBe(true);
        $this->lastCompleted()->shouldBe(null);
        $this->completed()->shouldBe(false);
        $this->stopped()->shouldBe(false);
    }

    public function it_can_be_marked_as_complete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->markAsComplete();

        $this->completed()->shouldBe(true);
        $this->streak()->days()->shouldBe(1);
    }

    public function it_can_be_marked_as_incomplete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->markAsComplete();
        $this->markAsIncomplete();

        $this->completed()->shouldBe(false);
        $this->streak()->days()->shouldBe(0);
    }

    public function it_cannot_mark_a_completed_habit_as_complete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->markAsComplete();
        $this->shouldThrow(HabitAlreadyCompleted::class)
            ->during('markAsComplete');
    }

    public function it_cannot_mark_an_incompleted_task_as_incomplete()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->shouldThrow(HabitAlreadyIncompleted::class)
            ->during('markAsIncomplete');
    }

    public function it_can_be_edited()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
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

    public function it_can_be_reset()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->reset();

        $this->completed()->shouldBe(false);
    }

    public function it_correctly_maintains_its_streak_when_reset()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);
        $this->markAsComplete();
        $this->completed()->shouldBe(true);
        $this->streak()->days()->shouldBe(1);

        $this->reset();

        $this->completed()->shouldBe(false);
        $this->streak()->days()->shouldBe(1);

        $this->reset();

        $this->completed()->shouldBe(false);
        $this->streak()->days()->shouldBe(0);
    }

    public function it_can_be_stopped()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->stop();
        $this->stopped()->shouldBe(true);
    }

    public function it_cannot_stop_an_already_stopped_habit()
    {
        $this->beConstructedThrough('start', [
            HabitId::generate(),
            3,
            'Read a book',
            new HabitFrequency('daily'),
        ]);

        $this->stop();

        $this->shouldThrow(HabitAlreadyStopped::class)
            ->duringStop();
    }

    public function it_can_be_serialized()
    {
        $this->beConstructedWith(
            $id = HabitId::generate(),
            3,
            $name = 'Read a book',
            $frequency = new HabitFrequency('daily'),
            $streak = new HabitStreak(),
        );

        $this->shouldImplement(\JsonSerializable::class);

        $this->jsonSerialize()->shouldBe([
            'id' => $id,
            'name' => $name,
            'frequency' => $frequency,
            'streak' => $streak,
            'completed' => false,
            'stopped' => false,
        ]);
    }
}
