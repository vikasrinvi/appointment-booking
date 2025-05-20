<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentBooked extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $recipientName,
        public string $recipientEmail
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointments.booked',
            with: [
                'appointment' => $this->appointment,
                'recipientName' => $this->recipientName,
                'recipientEmail' => $this->recipientEmail,
            ],
        );
    }
} 