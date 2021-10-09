<?php

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Tests\Support\HabitModelFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Infrastructure\Eloquent\HabitRepository as EloquentHabitRepository;

uses(RefreshDatabase::class);

it("can retrieve all the current user's habits", function () {
    $john = $this->login();
    $habits = EloquentHabit::factory(10)->for($john)->create();

    $results = resolve(EloquentHabitRepository::class)->all();

    expect($results)->toHaveCount(10);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($habits->pluck('id'));
    }
});

it("cannot retrieve another user's habits", function () {
    $john = $this->login();
    $mine = EloquentHabit::factory(10)->for($john)->create();
    $notMine = EloquentHabit::factory(10)->forUser()->create();

    $results = resolve(EloquentHabitRepository::class)->all();

    expect($results)->toHaveCount(10);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($mine->pluck('id'))
            ->id()->toString()->not->toBeIn($notMine->pluck('id'));
    }
});

it("can retrieve all the current user's habits for today", function () {
    $john = $this->login();
    $todays = EloquentHabit::factory(5)->for($john)->create([
        'frequency' => [
            'type' => 'weekly',
            'days' => [now()->dayOfWeek]
        ]
    ]);
    $tomorrows = EloquentHabit::factory(5)->for($john)->create([
        'frequency' => [
            'type' => 'weekly',
            'days' => [now()->addDay()->dayOfWeek]
        ]
    ]);

    $results = resolve(EloquentHabitRepository::class)->forToday();

    expect($results)->toHaveCount(5);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($todays->pluck('id'));
    }
});

it("can find a user's habit", function () {
    $john = $this->login();
    $habit = EloquentHabit::factory()->for($john)->create();

    $result = resolve(EloquentHabitRepository::class)->find(HabitId::fromString($habit->id));

    expect($result)
        ->toBeInstanceOf(Habit::class)
        ->id()->toString()->toBe($habit->id)
        ->name()->toBe($habit->name)
        ->frequency()->type()->toBe($habit->frequency->type)
        ->frequency()->days()->toBe($habit->frequency->days);
});

it('cannot find a non existent habit', function () {
    $this->login();

    expect(fn () =>
        resolve(EloquentHabitRepository::class)->find(HabitId::generate())
    )->toThrow(HabitNotFoundException::class);
});

it("cannot find another user's habit", function () {
    $this->login();
    $habit = EloquentHabit::factory()->forUser()->create();

    expect(fn () =>
        resolve(EloquentHabitRepository::class)->find(HabitId::fromString($habit->id))
    )->toThrow(\Exception::class);
});

it('can persist a habit', function () {
    $john = $this->login();
    $habit = HabitModelFactory::start();

    resolve(EloquentHabitRepository::class)->save($habit);

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id(),
        'name' => $habit->name(),
        'streak' => $habit->streak()->toString(),
        'frequency->type' => $habit->frequency()->type(),
        'last_completed' => null,
        'last_incompleted' => null,
        'stopped' => $habit->stopped(),
        'user_id' => $john->id
    ]);
    $this->assertEquals(
        $habit->frequency()->days(),
        EloquentHabit::find($habit->id())->frequency->days
    );
});
