<?php

namespace HabitTracking\Infrastructure;

use App\Models\User;
use Database\Factories\HabitFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'streak',
        'frequency',
        'last_completed',
        'last_incompleted',
        'user_id',
        'stopped',
    ];

    protected $casts = [
        'frequency' => 'object',
        'last_completed' => 'date',
        'last_incompleted' => 'date',
        'stopped' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): Factory
    {
        return HabitFactory::new();
    }
}
