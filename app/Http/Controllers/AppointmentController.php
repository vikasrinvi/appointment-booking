<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $appointments = $this->appointmentService->getUpcomingAppointments($request->user());
        
        return response()->json([
            'appointments' => $appointments
        ]);
    }

    public function store(AppointmentRequest $request): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->createAppointment(
                user: $request->user(),
                title: $request->title,
                description: $request->description,
                startTime: Carbon::parse($request->start_time),
                endTime: Carbon::parse($request->end_time),
                timezone: $request->timezone,
                reminderMinutes: $request->reminder_minutes ?? 30,
                guests: $request->guests ?? []
            );

            return response()->json([
                'message' => 'Appointment created successfully',
                'appointment' => $appointment
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        try {
            $this->appointmentService->cancelAppointment($appointment);

            return response()->json([
                'message' => 'Appointment cancelled successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
} 