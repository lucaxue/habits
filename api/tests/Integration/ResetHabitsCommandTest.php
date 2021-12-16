<?php

use HabitTracking\Presentation\HabitFinder;
use Tests\Support\HabitFactory;

it('resets all habits', function () {
    HabitFactory::count(10)->completed();

    $this
        ->artisan('habits:reset')
        ->expectsOutput('SUCCESS: 10 Habits have been reset.')
        ->assertExitCode(0);

    $habits = resolve(HabitFinder::class)->all();
    expect($habits)->each(function ($habit) {
        $habit->completed()->toBeFalse();
    });
});
