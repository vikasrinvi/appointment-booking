<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private AppointmentService $appointmentService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->appointmentService = app(AppointmentService::class);
    }

    public function test_can_create_appointment_on_weekday(): void
    {
        $startTime = Carbon::now()->nextWeekday();
        $endTime = $startTime->copy()->addHour();

        $appointment = $this->appointmentService->createAppointment(
            user: $this->user,
            title: 'Test Appointment',
            description: 'Test Description',
            startTime: $startTime,
            endTime: $endTime,
            timezone: 'UTC',
            reminderMinutes: 30
        );

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'title' => 'Test Appointment',
            'description' => 'Test Description',
        ]);
    }

    public function test_cannot_create_appointment_on_weekend(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $startTime = Carbon::now()->nextWeekendDay();
        $endTime = $startTime->copy()->addHour();

        $this->appointmentService->createAppointment(
            user: $this->user,
            title: 'Test Appointment',
            description: 'Test Description',
            startTime: $startTime,
            endTime: $endTime,
            timezone: 'UTC',
            reminderMinutes: 30
        );
    }

    public function test_can_cancel_appointment_30_minutes_before(): void
    {
        $startTime = Carbon::now()->addHours(2);
        $endTime = $startTime->copy()->addHour();

        $appointment = $this->appointmentService->createAppointment(
            user: $this->user,
            title: 'Test Appointment',
            description: 'Test Description',
            startTime: $startTime,
            endTime: $endTime,
            timezone: 'UTC',
            reminderMinutes: 30
        );

        $this->appointmentService->cancelAppointment($appointment);

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);
    }

    public function test_cannot_cancel_appointment_less_than_30_minutes_before(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $startTime = Carbon::now()->addMinutes(20);
        $endTime = $startTime->copy()->addHour();

        $appointment = $this->appointmentService->createAppointment(
            user: $this->user,
            title: 'Test Appointment',
            description: 'Test Description',
            startTime: $startTime,
            endTime: $endTime,
            timezone: 'UTC',
            reminderMinutes: 30
        );

        $this->appointmentService->cancelAppointment($appointment);
    }
} 