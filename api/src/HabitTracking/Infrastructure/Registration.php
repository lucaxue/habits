<?php

namespace HabitTracking\Infrastructure;

use Illuminate\Support\ServiceProvider;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Infrastructure\Eloquent\HabitRepository as EloquentHabitRepository;

class Registration extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            HabitRepository::class,
            EloquentHabitRepository::class
        );
    }
}
