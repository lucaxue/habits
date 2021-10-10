<?php

namespace HabitTracking\Infrastructure\Eloquent;

use Database\Factories\HabitFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'author_id',
        'name',
        'streak',
        'frequency',
        'last_completed',
        'last_incompleted',
        'stopped',
    ];

    protected $casts = [
        'frequency' => 'object',
        'last_completed' => 'date',
        'last_incompleted' => 'date',
        'stopped' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return HabitFactory::new();
    }
}
