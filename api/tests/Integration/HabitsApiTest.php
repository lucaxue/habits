<?php

use App\Models\User;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;
use Tests\Support\HabitFactory;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->john = $this->login();
});

test('one can retrieve all their habits', function () {
    $habits = HabitFactory::count(10)->start([
        'authorId' => $this->login()->id,
    ]);

    $response = getJson('api/habits')
        ->assertOk()
        ->assertJsonCount(10)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'streak',
                'completed',
                'frequency' => [
                    'type',
                    'days',
                ],
            ],
        ]);
    $this->assertEqualsCanonicalizing(
        collect($habits)->map->id(),
        collect($response->getData())->pluck('id'),
    );
});

test('one can retrieve all their habits for today', function () {
    $practiceGuitar = HabitFactory::start([
        'authorId' => $this->john->id,
        'name' => 'Practice Guitar',
        'frequency' => new HabitFrequency('weekly', [now()->subDay()->dayOfWeek]), // <- yesterday
    ]);
    $readBook = HabitFactory::start([
        'authorId' => $this->john->id,
        'name' => 'Read Book',
        'frequency' => new HabitFrequency('daily'), // <- daily
    ]);
    $morningRun = HabitFactory::start([
        'authorId' => $this->john->id,
        'name' => 'Morning Run',
        'frequency' => new HabitFrequency('weekly', [now()->dayOfWeek]), // <- today
    ]);

    getJson('api/habits/today')
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonFragment(['id' => $readBook->id()])
        ->assertJsonFragment(['id' => $morningRun->id()])
        ->assertJsonMissing(['id' => $practiceGuitar->id()]);
});

test('one can retrieve a habit', function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $this->john->id,
        'name' => 'Read Book',
        'frequency' => new HabitFrequency('daily'),
    ]);

    getJson("api/habits/{$id}")
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Read Book',
            'streak' => 'P0Y0M0D',
            'frequency' => [
                'type' => 'daily',
                'days' => null,
            ],
        ]);
});

test('one cannot retrive an unknown habit')
    ->getJson('api/habits/'.HabitId::generate())
    ->assertNotFound();

test('one can start a new habit')
    ->postJson('api/habits', [
        'name' => 'Practice Shutdown Ritual',
        'frequency' => ['type' => 'weekly', 'days' => [1, 2, 3, 4, 5]],
    ])
    ->assertCreated()
    ->assertJson([
        'name' => 'Practice Shutdown Ritual',
        'frequency' => ['type' => 'weekly', 'days' => [1, 2, 3, 4, 5]],
        'streak' => 'P0Y0M0D',
        'completed' => false,
    ]);

test('one can mark a habit as complete', function () {
    HabitFactory::incompleted([
        'id' => $id = HabitId::generate(),
        'authorId' => $this->john->id,
        'name' => 'Practice Shutdown Ritual',
        'streak' => HabitStreak::fromString('P0Y0M0D'),
    ]);

    putJson("api/habits/{$id}/complete")
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Practice Shutdown Ritual',
            'streak' => 'P0Y0M1D',
            'completed' => true,
        ]);
});

test('one can mark a habit as incomplete', function () {
    HabitFactory::completed([
        'id' => $id = HabitId::generate(),
        'authorId' => $this->john->id,
        'name' => 'Practice Shutdown Ritual',
        'streak' => HabitStreak::fromString('P0Y0M1D'),
    ]);

    putJson("api/habits/{$id}/incomplete")
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Practice Shutdown Ritual',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ]);
});

test('one can edit a habit', function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $this->john->id,
        'name' => 'Learning Arabic',
        'frequency' => new HabitFrequency('weekly', [1, 2, 3]),
    ]);

    putJson("api/habits/{$id}", [
            'name' => 'Learning Chinese',
            'frequency' => ['type' => 'daily', 'days' => null],
        ])
        ->assertOk()
        ->assertJsonFragment([
            'id' => $id,
            'name' => 'Learning Chinese',
            'frequency' => ['type' => 'daily', 'days' => null],
        ]);
});

test('one can stop a habit', function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $this->john->id,
    ]);

    deleteJson("api/habits/{$id}")->assertOk();

    $this->assertDatabaseHas('habits', [
        'id' => $id,
        'stopped' => true,
    ]);
});

test("one cannot manage another user's habit", function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => User::factory()->create(['name' => 'Jane'])->id,
    ]);

    collect([
        getJson("api/habits/{$id}"),
        putJson("api/habits/{$id}", [
            'name' => 'Read a book',
            'frequency' => ['type' => 'daily', 'days' => null],
        ]),
        putJson("api/habits/{$id}/complete"),
        putJson("api/habits/{$id}/incomplete"),
        deleteJson("api/habits/{$id}"),
    ])->each->assertUnauthorized();
});
