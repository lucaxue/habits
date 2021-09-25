<?php

it("retrieves today's habits", function () {
    $response = $this->getJson('api/habits/today');
    $response
        ->assertOk()
        ->assertJson([[
            'id' => 1,
            'name' => 'Read Book',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ], [
            'id' => 2,
            'name' => 'Learning Arabic',
            'streak' => 'P0Y0M1D',
            'completed' => true,
        ], [
            'id' => 3,
            'name' => 'Morning Run',
            'streak' => 'P0Y0M1D',
            'completed' => true,
        ]]);
});

it('retrieves all habits', function () {
    $response = $this->getJson('api/habits');
    $response
        ->assertOk()
        ->assertJson([[
            'id' => 1,
            'name' => 'Read Book',
            'frequency' => [
                'type' => 'daily',
                'on' => null,
            ]
        ], [
            'id' => 2,
            'name' => 'Learning Arabic',
            'frequency' => [
                'type' => 'weekly',
                'on' => [1, 2, 3],
            ]
        ], [
            'id' => 3,
            'name' => 'Morning Run',
            'frequency' => [
                'type' => 'daily',
                'on' => null,
            ]
        ], [
            'id' => 3,
            'name' => 'Call Mum',
            'frequency' => [
                'type' => 'weekly',
                'on' => [6],
            ]
        ]]);
});

it('can retrieve a habit', function () {
    $habit = [
        'id' => $id = 1,
        'name' => 'Read Book',
        'streak' => 'P0Y0M0D',
        'frequency' => [
            'type' => 'daily',
            'on' => null,
        ]
    ];

    $response = $this->getJson("api/habits/{$id}");
    $response
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Read Book',
            'streak' => 'P0Y0M0D',
            'frequency' => [
                'type' => 'daily',
                'on' => null,
            ]
        ]);
});

it('can start a new habit', function () {
    $response = $this->postJson('api/habits', $habit = [
        'name' => 'Practice Shutdown Ritual',
        'frequency' => [
            'type' => 'weekly',
            'on' => [1, 2, 3, 4, 5],
        ]
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('habits', $habit);
});

it('can mark a habit as complete', function () {
    $habit = [
        'id' => $id = 2,
        'name' => 'Practice Shutdown Ritual',
        'streak' => 'P0Y0M0D',
        'completed' => false,
    ];

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
    $habit = [
        'id' => $id = 2,
        'name' => 'Practice Shutdown Ritual',
        'streak' => 'P0Y0M1D',
        'completed' => true,
    ];

    $response = $this->putJson("api/habits/{$id}/miss");
    $response
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Practice Shutdown Ritual',
            'streak' => 'P0Y0M0D',
            'completed' => false,
        ]);
});

it('can edit a habit', function () {
    $habit = [
        'id' => $id = 2,
        'name' => 'Learning Arabic',
        'frequency' => [
            'type' => 'weekly',
            'on' => [1, 2, 3]
        ]
    ];

    $response = $this->putJson("api/habits/{$id}", [
        'name' => 'Learning Chinese',
        'frequency' => [
            'type' => 'daily',
            'on' => null
        ]
    ]);
    $response
        ->assertOk()
        ->assertJson([
            'id' => $id,
            'name' => 'Learning Chinese',
            'frequency' => [
                'type' => 'daily',
                'on' => null
            ]
        ]);
});
