<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminders for upcoming appointments';

    public function handle(): void
    {
        $now = Carbon::now();
        Appointment::query()
            ->where('start_time', '>', $now)
            ->where('start_time', '<=', $now->copy()->addMinutes(30))
            ->with(['user', 'guests'])
            ->each(function (Appointment $appointment) {
                // Send reminder to organizer
                Mail::to($appointment->user->email)
                    ->queue(new AppointmentReminder(
                        $appointment,
                        $appointment->user->name,
                        $appointment->user->email
                    ));

                // Send reminder to guests
                foreach ($appointment->guests as $guest) {
                    Mail::to($guest->email)
                        ->queue(new AppointmentReminder(
                            $appointment,
                            $guest->name ?? $guest->email,
                            $guest->email
                        ));
                }
            });
    }
} 