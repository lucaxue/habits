<?php

use App\Models\User;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Tests\Support\HabitModelFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use HabitTracking\Infrastructure\Eloquent\Habit as EloquentHabit;
use HabitTracking\Infrastructure\Eloquent\HabitRepository as EloquentHabitRepository;

uses(RefreshDatabase::class);

it("retrieves all habits by its author", function () {
    $habits = EloquentHabit::factory(10)->forAuthor(['id' => 1])->create();

    $results = resolve(EloquentHabitRepository::class)->all(1);

    expect($results)->toHaveCount(10);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($habits->pluck('id'));
    }
});

it("retrieves all habits for today by its author", function () {
    User::factory()->create(['id' => 1]);
    $todays = EloquentHabit::factory(5)->create([
        'frequency' => [
            'type' => 'weekly',
            'days' => [now()->dayOfWeek]
        ],
        'author_id' => 1,
    ]);
    $tomorrows = EloquentHabit::factory(5)->create([
        'frequency' => [
            'type' => 'weekly',
            'days' => [now()->addDay()->dayOfWeek]
        ],
        'author_id' => 1,
    ]);

    $results = resolve(EloquentHabitRepository::class)->all(1, forToday: true);

    expect($results)->toHaveCount(5);
    foreach ($results as $result) {
        expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($todays->pluck('id'));
    }
});

it("does not retrieve another author's habits", function () {
    $mine = EloquentHabit::factory(10)->forAuthor(['id' => 1])->create();
    $notMine = EloquentHabit::factory(10)->forAuthor(['id' => 2])->create();

    $results = resolve(EloquentHabitRepository::class)->all(1);

    $results->each(
        fn ($result) => expect($result)
            ->toBeInstanceOf(Habit::class)
            ->id()->toString()->toBeIn($mine->pluck('id'))
            ->id()->toString()->not->toBeIn($notMine->pluck('id'))
    );
});

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
