<?php

namespace HabitTracking\Presentation;

use Illuminate\Support\Collection;

class HabitFinder
{
    public function all(array $filters = []) : Collection
    {
        $query = Habit::where('stopped', false);

        if (isset($filters['author'])) {
            $query->where('author_id', $filters['author']);
        }

        if (isset($filters['today'])) {
            $query->where(function ($query) {
                $query
                    ->whereJsonContains('frequency->days', [now()->dayOfWeek])
                    ->orWhere('frequency->type', 'daily');
            });
        }

        return $query->get()->map->toModel();
    }
}
