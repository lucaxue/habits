<?php

use App\Models\User;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Tests\Support\HabitModelFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Infrastructure\Eloquent\HabitRepository as EloquentHabitRepository;

uses(RefreshDatabase::class);

it("can retrieve all habits", function () {
    $habits = EloquentHabit::factory(10)->create();

    $results = resolve(EloquentHabitRepository::class)->all();

    expect($results)->toHaveCount(10);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($habits->pluck('id'));
    }
});

it("can retrieve all habits for today", function () {
    $todays = EloquentHabit::factory(5)->create([
        'frequency' => [
            'type' => 'weekly',
            'days' => [now()->dayOfWeek]
        ]
    ]);
    $tomorrows = EloquentHabit::factory(5)->create([
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

it("can find a habit", function () {
    $habit = EloquentHabit::factory()->create();

    $result = resolve(EloquentHabitRepository::class)->find(HabitId::fromString($habit->id));

    expect($result)
        ->toBeInstanceOf(Habit::class)
        ->id()->toString()->toBe($habit->id)
        ->name()->toBe($habit->name)
        ->frequency()->type()->toBe($habit->frequency->type)
        ->frequency()->days()->toBe($habit->frequency->days);
});

it('can persist a habit', function () {
    $habit = HabitModelFactory::start(['authorId' => User::factory()->create()->id]);

    resolve(EloquentHabitRepository::class)->save($habit);

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id(),
        'author_id' => $habit->authorId(),
        'name' => $habit->name(),
        'streak' => $habit->streak()->toString(),
        'frequency->type' => $habit->frequency()->type(),
        'last_completed' => null,
        'last_incompleted' => null,
        'stopped' => $habit->stopped(),
    ]);
    $this->assertEquals(
        $habit->frequency()->days(),
        EloquentHabit::find($habit->id())->frequency->days
    );
});
