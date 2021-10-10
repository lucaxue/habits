<?php

use Tests\Support\HabitFactory;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a guest cannot access the api', function () {
    $responses = collect([
        $this->getJson('api/habits/today'),
        $this->getJson('api/habits'),
        $this->getJson('api/habits/' . HabitId::generate()),
        $this->postJson('api/habits', []),
        $this->putJson('api/habits/' . HabitId::generate(), []),
        $this->putJson('api/habits/' . HabitId::generate() . '/complete'),
        $this->putJson('api/habits/' . HabitId::generate() . '/incomplete'),
        $this->deleteJson('api/habits/' . HabitId::generate()),
    ]);

    $responses->each->assertUnauthorized();
});

test('one can retrieve all their habits for today', function () {
    $john = $this->login();
    HabitFactory::count(10)->start([
        'frequency' => new HabitFrequency('weekly', [now()->addDay()->dayOfWeek]),
        'authorId' => $john->id,
    ]);
    HabitFactory::many()->start([[
        'id' => $bookId = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Read Book',
        'frequency' => new HabitFrequency('daily')
    ], [
        'id' => $arabicId = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Learning Arabic',
        'frequency' => new HabitFrequency('weekly', [now()->dayOfWeek])
    ], [
        'id' => $runId = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Morning Run',
        'frequency' => new HabitFrequency('weekly', [now()->dayOfWeek])
    ]]);

    $response = $this->getJson('api/habits/today');

    $response
        ->assertOk()
        ->assertJsonCount(3)
        ->assertJsonFragment([
            'id' => $bookId,
            'name' => 'Read Book',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ])
        ->assertJsonFragment([
            'id' => $arabicId,
            'name' => 'Learning Arabic',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ])
        ->assertJsonFragment([
            'id' => $runId,
            'name' => 'Morning Run',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ]);
});

test('one can retrieve all their habits', function () {
    $john = $this->login();
    HabitFactory::many()->start([[
        'id' => $bookId =  HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Read Book',
        'frequency' => new HabitFrequency('daily')
    ], [
        'id' => $arabicId = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Learning Arabic',
        'frequency' => new HabitFrequency('weekly', [1, 2, 3])
    ], [
        'id' => $morningId = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Morning Run',
        'frequency' => new HabitFrequency('daily')
    ]]);

    $response = $this->getJson('api/habits');

    $response
        ->assertOk()
        ->assertJsonCount(3)
        ->assertJsonFragment([
            'id' => $bookId,
            'name' => 'Read Book',
            'frequency' => [
                'type' => 'daily',
                'days' => null,
            ]
        ])
        ->assertJsonFragment([
            'id' => $arabicId,
            'name' => 'Learning Arabic',
            'frequency' => [
                'type' => 'weekly',
                'days' => [1, 2, 3],
            ]
        ])
        ->assertJsonFragment([
            'id' => $morningId,
            'name' => 'Morning Run',
            'frequency' => [
                'type' => 'daily',
                'days' => null,
            ]
        ]);
});

test('one can retrieve a habit', function () {
    $john = $this->login();
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Read Book',
        'frequency' => new HabitFrequency('daily')
    ]);

    $response = $this->getJson("api/habits/{$id}");
    $response
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Read Book',
            'streak' => 'P0Y0M0D',
            'frequency' => [
                'type' => 'daily',
                'days' => null,
            ]
        ]);
});

test('retrieving a non existent habit returns a not found response', function () {
    $id = HabitId::generate();
    $john = $this->login();

    $response = $this->getJson("api/habits/{$id}");

    $response->assertNotFound();
});

test('one can start a new habit', function () {
    $john = $this->login();

    $response = $this->postJson('api/habits', [
        'name' => 'Practice Shutdown Ritual',
        'frequency' => [
            'type' => 'weekly',
            'days' => [1, 2, 3, 4, 5],
        ]
    ]);

    $response
        ->assertCreated()
        ->assertJson([
            'name' => 'Practice Shutdown Ritual',
            'frequency' => [
                'type' => 'weekly',
                'days' => [1, 2, 3, 4, 5],
            ],
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ]);
});

test('one can mark a habit as complete', function () {
    $john = $this->login();
    HabitFactory::incompleted([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Practice Shutdown Ritual',
        'streak' => HabitStreak::fromString('P0Y0M0D'),
    ]);

    $response = $this->putJson("api/habits/{$id}/complete");

    $response
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Practice Shutdown Ritual',
            'streak' => 'P0Y0M1D',
            'completed' => true,
        ]);
});

test('one can mark a habit as incomplete', function () {
    $john = $this->login();
    HabitFactory::completed([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Practice Shutdown Ritual',
        'streak' => HabitStreak::fromString('P0Y0M1D'),
    ]);

    $response = $this->putJson("api/habits/{$id}/incomplete");

    $response
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Practice Shutdown Ritual',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ]);
});

test('one can edit a habit', function () {
    $john = $this->login();
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id,
        'name' => 'Learning Arabic',
        'frequency' => new HabitFrequency('weekly', [1, 2, 3])
    ]);

    $response = $this->putJson("api/habits/{$id}", [
        'name' => 'Learning Chinese',
        'frequency' => [
            'type' => 'daily',
            'days' => null
        ]
    ]);

    $response
        ->assertOk()
        ->assertJsonFragment([
            'id' => $id,
            'name' => 'Learning Chinese',
            'frequency' => [
                'type' => 'daily',
                'days' => null
            ]
        ]);
});

test('one can stop a habit', function () {
    $john = $this->login();
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $john->id
    ]);

    $response = $this->deleteJson("api/habits/{$id}");

    $response->assertOk();
    $this->assertDatabaseHas('habits', [
        'id' => $id,
        'stopped' => true,
    ]);
});

test("one cannot manage another user's habit", function () {
    $jane = $this->login();
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $jane->id,
    ]);
    $john = $this->login();

    $responses = collect([
        $this->getJson("api/habits/{$id}"),
        $this->putJson("api/habits/{$id}", [
            'name' => 'Read a book',
            'frequency' => ['type' => 'daily', 'days' => null],
        ]),
        $this->putJson("api/habits/{$id}/complete"),
        $this->putJson("api/habits/{$id}/incomplete"),
        $this->deleteJson("api/habits/{$id}")
    ]);

    $responses->each->assertUnauthorized();
});
