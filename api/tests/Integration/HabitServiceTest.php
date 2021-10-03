<?php

use HabitTracking\Domain\Habit;
use Tests\Support\HabitModelFactory;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Application\HabitService;

beforeEach(function () {
    $this->repository = $this->createStub(HabitRepository::class);
    $this->service = new HabitService($this->repository);
});

it('can retrieve all habits', function () {
    $habits = HabitModelFactory::count(10)->start();

    $this->repository
        ->expects($this->once())
        ->method('all')
        ->willReturn($habits);

    $retrievedHabits = $this->service->retrieveHabits();

    expect($retrievedHabits)->toEqualCanonicalizing($habits);
});

it("can retrieve today's habit", function () {
    HabitModelFactory::count(5)->start([
        'frequency' => new HabitFrequency('daily')
    ]);

});

it('can retrieve a habit', function () {
	$habit = HabitModelFactory::start([
		'id' => $id = HabitId::generate()
	]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $retrievedHabit = $this->service->retrieveHabit($id->toString());

    expect($retrievedHabit)->toBe($habit);
});

it('can start a habit', function () {
    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->startHabit('Read a book', [
        'type' => 'weekly',
        'days' => [1, 2, 3]
    ]);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->name()->toBe('Read a book')
        ->frequency()->type()->toBe('weekly')
        ->frequency()->days()->toBe([1, 2, 3]);
});

it('can mark a habit as complete', function () {
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate()
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->markHabitAsComplete($id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->id()->toBe($id)
        ->completed()->toBeTrue()
        ->streak()->days()->toBe(1);
});

it('can mark a habit as incomplete', function () {
    $habit = HabitModelFactory::completed([
        'id' => $id = HabitId::generate()
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->markHabitAsIncomplete($id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->id()->toBe($id)
        ->completed()->toBeFalse()
        ->streak()->days()->toBe(0);
});

it('can edit a habit', function () {
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
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

    $habit = $this->service->editHabit($id, 'Read two books', [
        'type' => 'daily',
        'days' => null,
    ]);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->id()->toBe($id)
        ->name()->toBe('Read two books')
        ->frequency()->type()->toBe('daily')
        ->frequency()->days()->toBe(null);
});

it('can stop a habit', function () {
    $habit = HabitModelFactory::start([
        'id' => $id = HabitId::generate()
    ]);

    $this->repository
        ->expects($this->once())
        ->method('find')->with($id)
        ->willReturn($habit);

    $this->repository
        ->expects($this->once())
        ->method('save');

    $habit = $this->service->stopHabit($id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->stopped()->toBeTrue();
});
