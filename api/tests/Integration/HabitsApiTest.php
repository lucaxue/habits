<?php

use Tests\Support\HabitFactory;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Infrastructure\Eloquent\Habit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->login();
});

// it("retrieves today's habits", function () {
//     $response = $this->getJson('api/habits/today');
//     $response
//         ->assertOk()
//         ->assertJson([[
//             'id' => 1,
//             'name' => 'Read Book',
//             'streak' => 'P0Y0M0D',
//             'completed' => false,
//         ], [
//             'id' => 2,
//             'name' => 'Learning Arabic',
//             'streak' => 'P0Y0M1D',
//             'completed' => true,
//         ], [
//             'id' => 3,
//             'name' => 'Morning Run',
//             'streak' => 'P0Y0M1D',
//             'completed' => true,
//         ]]);
// });

it('retrieves all habits', function () {
    HabitFactory::many()->start([[
        'id' => $bookId =  HabitId::generate(),
        'name' => 'Read Book',
        'frequency' => new HabitFrequency('daily')
    ], [
        'id' => $arabicId = HabitId::generate(),
        'name' => 'Learning Arabic',
        'frequency' => new HabitFrequency('weekly', [1, 2, 3])
    ], [
        'id' => $morningId = HabitId::generate(),
        'name' => 'Morning Run',
        'frequency' => new HabitFrequency('daily')
    ]]);

    $response = $this->getJson('api/habits');

    $response
        ->assertOk()
        ->assertJson([[
            'id' => $bookId,
            'name' => 'Read Book',
            'frequency' => [
                'type' => 'daily',
                'days' => null,
            ]
        ], [
            'id' => $arabicId,
            'name' => 'Learning Arabic',
            'frequency' => [
                'type' => 'weekly',
                'days' => [1, 2, 3],
            ]
        ], [
            'id' => $morningId,
            'name' => 'Morning Run',
            'frequency' => [
                'type' => 'daily',
                'days' => null,
            ]
        ]]);
});

it('can retrieve a habit', function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
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

it('can start a new habit', function () {
    $response = $this->postJson('api/habits', $habit = [
        'name' => 'Practice Shutdown Ritual',
        'frequency' => $frequency = [
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

    $this->assertDatabaseHas('habits', [
        'name' => 'Practice Shutdown Ritual',
        'frequency' => json_encode($frequency),
    ]);
});

it('can mark a habit as complete', function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'name' => 'Practice Shutdown Ritual',
        'streak' => HabitStreak::fromString('P0Y0M0D'),
        'completed' => false,
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

it('can mark a habit as incomplete', function () {
    HabitFactory::start([
        'id' => $id = HabitId::generate(),
        'name' => 'Practice Shutdown Ritual',
        'streak' => HabitStreak::fromString('P0Y0M1D'),
        'completed' => true,
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

// it('can edit a habit', function () {
//     $habit = [
//         'id' => $id = 2,
//         'name' => 'Learning Arabic',
//         'frequency' => [
//             'type' => 'weekly',
//             'days' => [1, 2, 3]
//         ]
//     ];

//     $response = $this->putJson("api/habits/{$id}", [
//         'name' => 'Learning Chinese',
//         'frequency' => [
//             'type' => 'daily',
//             'days' => null
//         ]
//     ]);
//     $response
//         ->assertOk()
//         ->assertJson([
//             'id' => $id,
//             'name' => 'Learning Chinese',
//             'frequency' => [
//                 'type' => 'daily',
//                 'days' => null
//             ]
//         ]);
// });
