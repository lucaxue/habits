<?php

use App\Models\User;
use HabitTracking\Domain\Habit;
use Tests\Support\HabitFactory;
use HabitTracking\Domain\HabitId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use HabitTracking\Infrastructure\Habit as EloquentHabit;
use HabitTracking\Infrastructure\EloquentHabitRepository;

uses(RefreshDatabase::class);

it('can find a habit', function () {
    $habit = EloquentHabit::factory()->create();

    $result = (new EloquentHabitRepository)->find(HabitId::fromString($habit->id));

    expect($result)
        ->toBeInstanceOf(Habit::class)
        ->id()->toString()->toBe($habit->id)
        ->name()->toBe($habit->name)
        ->frequency()->type()->toBe($habit->frequency->type)
        ->frequency()->days()->toBe($habit->frequency->days);
});

it('can retrieve all habits', function () {
    $habits = EloquentHabit::factory(10)->create();

    $results = (new EloquentHabitRepository)->all();

    foreach ($results as $result){
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($habits->pluck('id'));
    }
});

it('can persist a habit', function () {
    $this->actingAs(User::factory()->create());

    $habit = HabitFactory::start();

    (new EloquentHabitRepository)->save($habit);

    $this->assertDatabaseHas('habits',[
        'id' => $habit->id()->toString(),
        'name' => $habit->name(),
        'streak' => $habit->streak()->toString(),
        'frequency' => json_encode($habit->frequency()),
        'last_completed' => null,
        'last_incompleted' => null,
        'stopped' => $habit->stopped(),
    ]);
});
