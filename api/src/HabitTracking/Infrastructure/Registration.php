<?php

namespace HabitTracking\Infrastructure;

use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Infrastructure\Eloquent\HabitRepository as EloquentHabitRepository;
use Illuminate\Support\ServiceProvider;

class Registration extends ServiceProvider
{
    public function register() : void
    {
        $this->app->bind(
            HabitRepository::class,
            EloquentHabitRepository::class
        );
    }
}
