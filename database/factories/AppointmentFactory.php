<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startTime = Carbon::now()->addDays(rand(1, 30))->setHour(rand(9, 17))->setMinute(0);
        $endTime = $startTime->copy()->addHour();

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'timezone' => 'UTC',
            'reminder_minutes' => 30,
        ];
    }
} 