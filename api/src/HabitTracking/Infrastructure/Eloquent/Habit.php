<?php

namespace HabitTracking\Infrastructure\Eloquent;

use App\Models\User;
use Database\Factories\HabitFactory;
use HabitTracking\Domain\Habit as HabitModel;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitId;
use HabitTracking\Domain\HabitStreak;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Habit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

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
        'stopped' => 'boolean',
    ];

    public function toModel() : HabitModel
    {
        return new HabitModel(
            HabitId::fromString($this->id),
            $this->author_id,
            $this->name,
            new HabitFrequency(...(array) $this->frequency),
            HabitStreak::fromString($this->streak),
            $this->stopped,
            $this->last_completed,
        );
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    protected static function newFactory() : Factory
    {
        return HabitFactory::new();
    }
}
