<?php

use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Facades\DB;
use Tests\Support\HabitModelFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Infrastructure\Eloquent\HabitRepository as EloquentHabitRepository;

uses(RefreshDatabase::class);

it("can find a user's habit", function () {
    $john = $this->login();
    $habit = EloquentHabit::factory()->for($john)->create();

    $result = (new EloquentHabitRepository)->find(HabitId::fromString($habit->id));

    expect($result)
        ->toBeInstanceOf(Habit::class)
        ->id()->toString()->toBe($habit->id)
        ->name()->toBe($habit->name)
        ->frequency()->type()->toBe($habit->frequency->type)
        ->frequency()->days()->toBe($habit->frequency->days);
});

it('cannot find a non existent habit', function () {
    $this->login();

    expect(function () {
        (new EloquentHabitRepository)->find(HabitId::generate());
    })->toThrow(\Exception::class);
});

it("cannot find another user's habit", function () {
    $john = $this->login();
    $habit = EloquentHabit::factory()->forUser()->create();

    expect(function () use ($habit) {
        (new EloquentHabitRepository)->find(HabitId::fromString($habit->id));
    })->toThrow(\Exception::class);
});

it("can retrieve all user's habits", function () {
    $john = $this->login();
    $habits = EloquentHabit::factory(10)->for($john)->create();

    $results = (new EloquentHabitRepository)->all();

    expect($results)->toHaveCount(10);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($habits->pluck('id'));
    }
});

it("can retrieve all user's habits for today", function () {
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

    $results = (new EloquentHabitRepository)->forToday();

    expect($results)->toHaveCount(5);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($todays->pluck('id'));
    }
});

it("cannot retrieve another user's habits", function () {
    $john = $this->login();
    $mine = EloquentHabit::factory(10)->for($john)->create();
    $notMine = EloquentHabit::factory(10)->forUser()->create();

    $results = (new EloquentHabitRepository)->all();

    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($mine->pluck('id'))
            ->id()->toString()->not->toBeIn($notMine->pluck('id'));
    }
});

it('can persist a habit', function () {
    $john = $this->login();
    $habit = HabitModelFactory::start();

    (new EloquentHabitRepository)->save($habit);

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id(),
        'name' => $habit->name(),
        'streak' => $habit->streak()->toString(),
        'last_completed' => null,
        'last_incompleted' => null,
        'stopped' => $habit->stopped(),
        'user_id' => $john->id
    ]);
    expect(EloquentHabit::find($habit->id())->frequency)
        ->type->toBe($habit->frequency()->type())
        ->days->toBe($habit->frequency()->days());
});
