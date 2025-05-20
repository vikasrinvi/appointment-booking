<?php

namespace Tests\Feature;

use App\Mail\AppointmentBooked;
use App\Mail\AppointmentCancelled;
use App\Models\Appointment;
use App\Models\AppointmentGuest;
use App\Models\User;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private AppointmentService $appointmentService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->appointmentService = app(AppointmentService::class);
        Mail::fake();
    }

    public function test_sends_booking_notification_to_organizer(): void
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

        Mail::assertQueued(AppointmentBooked::class, function ($mail) {
            return $mail->hasTo($this->user->email) &&
                   $mail->appointment->title === 'Test Appointment';
        });
    }

    public function test_sends_booking_notification_to_guests(): void
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
            reminderMinutes: 30,
            guests: [
                ['email' => 'guest1@example.com', 'name' => 'Guest One'],
                ['email' => 'guest2@example.com', 'name' => 'Guest Two'],
            ]
        );

        Mail::assertQueued(AppointmentBooked::class, function ($mail) {
            return $mail->hasTo('guest1@example.com') &&
                   $mail->recipientName === 'Guest One';
        });

        Mail::assertQueued(AppointmentBooked::class, function ($mail) {
            return $mail->hasTo('guest2@example.com') &&
                   $mail->recipientName === 'Guest Two';
        });
    }

    public function test_sends_cancellation_notification_to_organizer(): void
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

        Mail::assertQueued(AppointmentCancelled::class, function ($mail) {
            return $mail->hasTo($this->user->email) &&
                   $mail->appointment->title === 'Test Appointment';
        });
    }

    public function test_sends_cancellation_notification_to_guests(): void
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
            reminderMinutes: 30,
            guests: [
                ['email' => 'guest1@example.com', 'name' => 'Guest One'],
                ['email' => 'guest2@example.com', 'name' => 'Guest Two'],
            ]
        );

        $this->appointmentService->cancelAppointment($appointment);

        Mail::assertQueued(AppointmentCancelled::class, function ($mail) {
            return $mail->hasTo('guest1@example.com') &&
                   $mail->recipientName === 'Guest One';
        });

        Mail::assertQueued(AppointmentCancelled::class, function ($mail) {
            return $mail->hasTo('guest2@example.com') &&
                   $mail->recipientName === 'Guest Two';
        });
    }
} 