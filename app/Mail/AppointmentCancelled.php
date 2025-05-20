<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelled extends Mailable
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
            subject: 'Appointment Cancellation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointments.cancelled',
            with: [
                'appointment' => $this->appointment,
                'recipientName' => $this->recipientName,
                'recipientEmail' => $this->recipientEmail,
            ],
        );
    }
} 