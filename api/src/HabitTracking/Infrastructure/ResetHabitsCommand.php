<?php

namespace HabitTracking\Infrastructure;

use HabitTracking\Application\HabitService;
use HabitTracking\Presentation\HabitFinder;
use Illuminate\Console\Command;

class ResetHabitsCommand extends Command
{
    protected $signature = 'habits:reset';

    protected $description = 'Reset all habits.';

    public function handle(
        HabitService $service,
        HabitFinder $finder,
    ) : int {

        $habits = $finder->all();

        $habits->each(function ($habit) use ($service) {
            $service->resetHabit($habit->id());
        });

        $this->info('SUCCESS: 10 Habits have been reset.');
        return Command::SUCCESS;
    }
}
