<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TimeSlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeSlotTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private TimeSlotService $timeSlotService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->timeSlotService = app(TimeSlotService::class);
    }

    public function test_can_create_time_slot(): void
    {
        $timeSlot = $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Monday',
            startTime: Carbon::parse('09:00'),
            endTime: Carbon::parse('10:00')
        );

        $this->assertDatabaseHas('time_slots', [
            'id' => $timeSlot->id,
            'user_id' => $this->user->id,
            'day_of_week' => 'Monday',
            'is_available' => true,
        ]);
    }

    public function test_cannot_create_time_slot_on_weekend(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Saturday',
            startTime: Carbon::parse('09:00'),
            endTime: Carbon::parse('10:00')
        );
    }

    public function test_cannot_create_overlapping_time_slots(): void
    {
        $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Monday',
            startTime: Carbon::parse('09:00'),
            endTime: Carbon::parse('10:00')
        );

        $this->expectException(\InvalidArgumentException::class);

        $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Monday',
            startTime: Carbon::parse('09:30'),
            endTime: Carbon::parse('10:30')
        );
    }

    public function test_can_update_time_slot_availability(): void
    {
        $timeSlot = $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Monday',
            startTime: Carbon::parse('09:00'),
            endTime: Carbon::parse('10:00')
        );

        $this->timeSlotService->updateTimeSlotAvailability($timeSlot, false);

        $this->assertDatabaseHas('time_slots', [
            'id' => $timeSlot->id,
            'is_available' => false,
        ]);
    }

    public function test_can_get_available_time_slots(): void
    {
        $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Monday',
            startTime: Carbon::parse('09:00'),
            endTime: Carbon::parse('10:00')
        );

        $this->timeSlotService->createTimeSlot(
            user: $this->user,
            dayOfWeek: 'Monday',
            startTime: Carbon::parse('11:00'),
            endTime: Carbon::parse('12:00')
        );

        $timeSlots = $this->timeSlotService->getAvailableTimeSlots($this->user, 'Monday');

        $this->assertCount(2, $timeSlots);
    }
} 