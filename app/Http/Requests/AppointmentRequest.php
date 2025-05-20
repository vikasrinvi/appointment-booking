<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    { 
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'timezone' => ['required', 'string', 'timezone'],
            'reminder_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'guests' => ['nullable', 'array'],
            'guests.*.email' => ['required', 'email'],
            'guests.*.name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.after' => 'The appointment must start in the future.',
            'end_time.after' => 'The end time must be after the start time.',
            'reminder_minutes.min' => 'The reminder must be at least 1 minute before the appointment.',
            'reminder_minutes.max' => 'The reminder cannot be more than 24 hours before the appointment.',
        ];
    }
} 