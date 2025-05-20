<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Services\TimeSlotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function __construct(
        private TimeSlotService $timeSlotService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $dayOfWeek = $request->input('day_of_week', Carbon::now()->format('l'));
        $timeSlots = $this->timeSlotService->getAvailableTimeSlots($request->user(), $dayOfWeek);

        return response()->json([
            'time_slots' => $timeSlots
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'day_of_week' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        try {
            $timeSlot = $this->timeSlotService->createTimeSlot(
                user: $request->user(),
                dayOfWeek: $request->day_of_week,
                startTime: Carbon::parse($request->start_time),
                endTime: Carbon::parse($request->end_time)
            );

            return response()->json([
                'message' => 'Time slot created successfully',
                'time_slot' => $timeSlot
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, TimeSlot $timeSlot): JsonResponse
    {
        $request->validate([
            'is_available' => ['required', 'boolean'],
        ]);

        $this->timeSlotService->updateTimeSlotAvailability($timeSlot, $request->is_available);

        return response()->json([
            'message' => 'Time slot updated successfully',
            'time_slot' => $timeSlot
        ]);
    }

    public function destroy(TimeSlot $timeSlot): JsonResponse
    {
        $timeSlot->delete();

        return response()->json([
            'message' => 'Time slot deleted successfully'
        ]);
    }
} 