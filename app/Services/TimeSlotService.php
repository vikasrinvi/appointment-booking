<?php

namespace App\Services;

use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeSlotService
{
    public function createTimeSlot(
        User $user,
        string $dayOfWeek,
        Carbon $startTime,
        Carbon $endTime
    ): TimeSlot {
        $this->validateTimeSlot($user, $dayOfWeek, $startTime, $endTime);

        return TimeSlot::create([
            'user_id' => $user->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_available' => true,
        ]);
    }

    public function getAvailableTimeSlots(User $user, string $dayOfWeek): Collection
    {
        return $user->timeSlots()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();
    }

    public function updateTimeSlotAvailability(TimeSlot $timeSlot, bool $isAvailable): void
    {
        $timeSlot->update(['is_available' => $isAvailable]);
    }

    private function validateTimeSlot(User $user, string $dayOfWeek, Carbon $startTime, Carbon $endTime): void
    {
        if (!in_array($dayOfWeek, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])) {
            throw new \InvalidArgumentException('Time slots can only be created for weekdays.');
        }

        if ($startTime->greaterThanOrEqualTo($endTime)) {
            throw new \InvalidArgumentException('Start time must be before end time.');
        }

        // Check for overlapping time slots
        $existingSlots = $user->timeSlots()
            ->where('day_of_week', $dayOfWeek)
            ->get();

        $newSlot = new TimeSlot([
            'start_time' => $startTime,
            'end_time' => $endTime,
            'day_of_week' => $dayOfWeek,
        ]);

        foreach ($existingSlots as $slot) {
            if ($newSlot->overlaps($slot)) {
                throw new \InvalidArgumentException('This time slot overlaps with an existing slot.');
            }
        }
    }
} 