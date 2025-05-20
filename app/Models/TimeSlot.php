<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'day_of_week',
        'is_available',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_available' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    public function overlaps(TimeSlot $other): bool
    {
        return $this->day_of_week === $other->day_of_week &&
            (
                ($this->start_time <= $other->start_time && $this->end_time > $other->start_time) ||
                ($this->start_time < $other->end_time && $this->end_time >= $other->end_time) ||
                ($this->start_time >= $other->start_time && $this->end_time <= $other->end_time)
            );
    }
} 