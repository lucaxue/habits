<?php

use App\Models\User;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Tests\Support\HabitModelFactory;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Application\HabitService;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;

beforeEach(function () {
    $this->repository = $this->createStub(HabitRepository::class);
    $this->service = new HabitService($this->repository);
});

it("can retrieve all of a user's habits", function () {
    $john = User::factory()->make(['id' => 1]);
    $habits = HabitModelFactory::count(10)->start(['authorId' => $john->id]);

    $this->repository
        ->expects($this->once())
        ->method('all')
        ->willReturn($habits);

    $retrievedHabits = $this->service->retrieveHabits($john->id);

    expect($retrievedHabits)->toEqualCanonicalizing($habits);
});

it("can retrieve a user's habits for today", function () {
    $john = User::factory()->make(['id' => 1]);
    $todays = HabitModelFactory::count(5)->start([
        'authorId' => $john->id,
        'frequency' => new HabitFrequency('weekly', [
            now()->dayOfWeek
        ])
    ]);
    $tomorrows = HabitModelFactory::count(5)->start([
        'authorId' => $john->id,
        'frequency' => new HabitFrequency('weekly', [
            now()->addDay()->dayOfWeek
        ])
    ]);

    $this->repository
        ->expects($this->once())
        ->method('forToday')
        ->willReturn($todays);

    $retrievedHabits = $this->service->retrieveHabitsForToday($john->id);

    expect($retrievedHabits)->toEqualCanonicalizing($todays);
});

it("does not retrieve another user's habits", function () {
    $john = User::factory()->make(['id' => 1]);
    $mine = HabitModelFactory::count(10)->start([
        'authorId' => $john->id,
        'frequency' => new HabitFrequency('daily'),
    ]);
    $jane = User::factory()->make(['id' => 2]);
    $notMine = HabitModelFactory::count(10)->start([
        'authorId' => $jane->id,
        'frequency' => new HabitFrequency('daily'),
    ]);

    $this->repository
        ->expects($this->once())
        ->method('all')
        ->willReturn($mine->merge($notMine));

    $this->repository
        ->expects($this->once())
        ->method('forToday')
        ->willReturn($mine->merge($notMine));

    $retrievedHabits = $this->service->retrieveHabits($john->id);
    $retrievedHabitsForToday = $this->service->retrieveHabitsForToday($john->id);

    expect($retrievedHabits)->toEqual($mine);
    expect($retrievedHabitsForToday)->toEqual($mine);
    expect($retrievedHabits)->not->toEqual($notMine);
    expect($retrievedHabitsForToday)->not->toEqual($notMine);
});

it("can retrieve a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $retrievedHabit = $this->service->retrieveHabit($id->toString(), $john->id);

    expect($retrievedHabit)->toBe($habit);
});

it('cannot retrieve a non existent habit', function () {
    $john = User::factory()->make(['id' => 1]);
    $id = HabitId::generate();

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn(null);

    expect(fn () => $this->service->retrieveHabit($id->toString(), $john->id))
        ->toThrow(HabitNotFoundException::class);
});

it("can start a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->startHabit(
        'Read a book',
        ['type' => 'weekly', 'days' => [1, 2, 3]],
        $john->id,
    );

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->name()->toBe('Read a book')
        ->frequency()->type()->toBe('weekly')
        ->frequency()->days()->toBe([1, 2, 3]);
});

it("can mark a user's habit as complete", function () {
    $john = User::factory()->make(['id' => 1]);
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->markHabitAsComplete($id, $john->id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->id()->toBe($id)
        ->completed()->toBeTrue()
        ->streak()->days()->toBe(1);
});

it("can mark a user's habit as incomplete", function () {
    $john = User::factory()->make(['id' => 1]);
    $habit = HabitModelFactory::completed([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->markHabitAsIncomplete($id, $john->id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->id()->toBe($id)
        ->completed()->toBeFalse()
        ->streak()->days()->toBe(0);
});

it("can edit a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Read a book',
        'frequency' => new HabitFrequency('weekly', [1])
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->editHabit(
        $id,
        'Read two books',
        ['type' => 'daily', 'days' => null],
        $john->id
    );

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->id()->toBe($id)
        ->name()->toBe('Read two books')
        ->frequency()->type()->toBe('daily')
        ->frequency()->days()->toBe(null);
});

it("can stop a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->stopHabit($id, $john->id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->stopped()->toBeTrue();
});

it("cannot manage another user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $jane = User::factory()->make(['id' => 2]);
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $jane->id
    ]);

    $this->repository
        ->expects($this->exactly(5))
        ->method('find')->with($id)
        ->willReturn($habit);

    $actions = collect([
        fn () => $this->service->retrieveHabit($id, $john->id),
        fn () => $this->service->markHabitAsComplete($id, $john->id),
        fn () => $this->service->markHabitAsIncomplete($id, $john->id),
        fn () => $this->service->editHabit($id, 'name', ['type' => 'daily', 'days' => null], $john->id),
        fn () => $this->service->stopHabit($id, $john->id),
    ]);

    $actions->each(fn ($action) => expect($action)->toThrow(\Exception::class));
});
