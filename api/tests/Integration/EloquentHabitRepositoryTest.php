<?php

use App\Models\User;
use HabitTracking\Domain\Exceptions\HabitNotFound;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use HabitTracking\Infrastructure\EloquentHabitRepository;
use HabitTracking\Presentation\Habit as EloquentHabit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\HabitModelFactory;

uses(RefreshDatabase::class);

it('finds a habit', function () {
    $habit = EloquentHabit::factory()->create();

    $result = resolve(EloquentHabitRepository::class)->find(HabitId::fromString($habit->id));

    expect($result)
        ->toBeInstanceOf(Habit::class)
        ->id()->toString()->toBe($habit->id)
        ->name()->toBe($habit->name)
        ->frequency()->type()->toBe($habit->frequency->type)
        ->frequency()->days()->toBe($habit->frequency->days);
});

it('throws a not found exception when finding non existent habit', function () {
    $id = HabitId::generate();

    expect(fn () => resolve(EloquentHabitRepository::class)->find($id))
        ->toThrow(HabitNotFound::class, $id->toString());
});

it('persists a habit', function () {
    $habit = HabitModelFactory::start(['authorId' => User::factory()->create()->id]);

    resolve(EloquentHabitRepository::class)->save($habit);

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id(),
        'author_id' => $habit->authorId(),
        'name' => $habit->name(),
        'streak' => $habit->streak()->toString(),
        'frequency->type' => $habit->frequency()->type(),
        'last_completed' => null,
        'stopped' => $habit->stopped(),
    ]);
    $this->assertEquals(
        $habit->frequency()->days(),
        EloquentHabit::find($habit->id())->frequency->days
    );
});
