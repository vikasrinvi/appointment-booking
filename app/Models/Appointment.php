<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'timezone',
        'reminder_minutes',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(AppointmentGuest::class);
    }

    public function canBeCancelled(): bool
    {
        return now()->diffInMinutes($this->start_time) >= 30;
    }

    public function isOnWeekday(): bool
    {
        return !in_array($this->start_time->dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
    }

    public function getFormattedStartTimeAttribute(): string
    {
        return $this->start_time->setTimezone($this->timezone)->format('F j, Y g:i A');
    }

    public function getFormattedEndTimeAttribute(): string
    {
        return $this->end_time->setTimezone($this->timezone)->format('F j, Y g:i A');
    }
} 