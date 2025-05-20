<?php

namespace Database\Factories;

use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    public function definition(): array
    {
        $startHour = rand(9, 16);
        $startTime = Carbon::createFromTime($startHour, 0, 0);
        $endTime = $startTime->copy()->addHour();

        return [
            'user_id' => User::factory(),
            'day_of_week' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_available' => true,
        ];
    }
} 