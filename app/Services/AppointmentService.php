<?php

namespace App\Services;

use App\Mail\AppointmentBooked;
use App\Mail\AppointmentCancelled;
use App\Models\Appointment;
use App\Models\AppointmentGuest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class AppointmentService
{
    public function createAppointment(
        User $user,
        string $title,
        string $description,
        Carbon $startTime,
        Carbon $endTime,
        string $timezone,
        int $reminderMinutes,
        array $guests = []
    ): Appointment {
        if (!$this->isValidWeekday($startTime)) {
            throw new \InvalidArgumentException('Appointments can only be booked on weekdays (Monday to Friday).');
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'title' => $title,
            'description' => $description,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'timezone' => $timezone,
            'reminder_minutes' => $reminderMinutes,
        ]);

        $this->addGuests($appointment, $guests);
        $this->sendBookingNotifications($appointment);

        return $appointment;
    }

    public function cancelAppointment(Appointment $appointment): void
    {
        if (!$appointment->canBeCancelled()) {
            throw new \InvalidArgumentException('Appointments can only be cancelled 30 minutes before the scheduled time.');
        }

        $this->sendCancellationNotifications($appointment);
        $appointment->delete();
    }

    public function getUpcomingAppointments(User $user): Collection
    {
        return $user->appointments()
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
    }

    private function isValidWeekday(Carbon $date): bool
    {
        return !in_array($date->dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
    }

    private function addGuests(Appointment $appointment, array $guests): void
    {
        foreach ($guests as $guest) {
            AppointmentGuest::create([
                'appointment_id' => $appointment->id,
                'email' => $guest['email'],
                'name' => $guest['name'] ?? null,
            ]);
        }
    }

    private function sendBookingNotifications(Appointment $appointment): void
    {
        // Send to organizer
        Mail::to($appointment->user->email)
            ->queue(new AppointmentBooked(
                $appointment,
                $appointment->user->name,
                $appointment->user->email
            ));

        // Send to guests
        foreach ($appointment->guests as $guest) {
            Mail::to($guest->email)
                ->queue(new AppointmentBooked(
                    $appointment,
                    $guest->name ?? $guest->email,
                    $guest->email
                ));
        }
    }

    private function sendCancellationNotifications(Appointment $appointment): void
    {
        // Send to organizer
        Mail::to($appointment->user->email)
            ->queue(new AppointmentCancelled(
                $appointment,
                $appointment->user->name,
                $appointment->user->email
            ));

        // Send to guests
        foreach ($appointment->guests as $guest) {
            Mail::to($guest->email)
                ->queue(new AppointmentCancelled(
                    $appointment,
                    $guest->name ?? $guest->email,
                    $guest->email
                ));
        }
    }
} 