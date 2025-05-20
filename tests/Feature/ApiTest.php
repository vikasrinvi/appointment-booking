<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_list_appointments(): void
    {
        Appointment::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'appointments');
    }

    public function test_can_create_appointment(): void
    {
        $startTime = Carbon::now()->nextWeekday();
        $endTime = $startTime->copy()->addHour();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/appointments', [
                'title' => 'Test Appointment',
                'description' => 'Test Description',
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s'),
                'timezone' => 'UTC',
                'reminder_minutes' => 30,
                'guests' => [
                    ['email' => 'guest@example.com', 'name' => 'Test Guest'],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'appointment' => [
                    'id',
                    'title',
                    'description',
                    'start_time',
                    'end_time',
                ],
            ]);
    }

    public function test_can_cancel_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => Carbon::now()->addHours(2),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Appointment cancelled successfully']);

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);
    }

    public function test_can_list_time_slots(): void
    {
        TimeSlot::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'day_of_week' => 'Monday',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/time-slots?day_of_week=Monday');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'time_slots');
    }

    public function test_can_create_time_slot(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/time-slots', [
                'day_of_week' => 'Monday',
                'start_time' => '09:00',
                'end_time' => '10:00',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'time_slot' => [
                    'id',
                    'day_of_week',
                    'start_time',
                    'end_time',
                    'is_available',
                ],
            ]);
    }

    public function test_can_update_time_slot(): void
    {
        $timeSlot = TimeSlot::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/time-slots/' . $timeSlot->id, [
                'is_available' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Time slot updated successfully',
                'time_slot' => [
                    'is_available' => false,
                ],
            ]);
    }

    public function test_can_delete_time_slot(): void
    {
        $timeSlot = TimeSlot::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/time-slots/' . $timeSlot->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Time slot deleted successfully']);

        $this->assertDatabaseMissing('time_slots', [
            'id' => $timeSlot->id,
        ]);
    }
} 