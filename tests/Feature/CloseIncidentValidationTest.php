<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CloseIncidentValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that delay_reason is required when incident duration exceeds 5 hours.
     */
    public function test_delay_reason_required_when_duration_exceeds_5_hours(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'Low',
            'started_at' => now()->subHours(6),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(6)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'Low',
            // delay_reason is missing - should cause validation error
        ]);

        $response->assertStatus(302); // Redirect back with errors
        $response->assertSessionHasErrors(['delay_reason']);
    }

    /**
     * Test that delay_reason is not required when incident duration is under 5 hours.
     */
    public function test_delay_reason_not_required_when_duration_under_5_hours(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'Low',
            'started_at' => now()->subHours(3),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(3)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'Low',
            // delay_reason is not provided - should be OK
        ]);

        $response->assertRedirect(route('incidents.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test that travel_time and work_time are required for Medium severity incidents when closing.
     */
    public function test_travel_and_work_time_required_for_medium_severity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'Medium',
            'started_at' => now()->subHours(2),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(2)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'Medium',
            // travel_time and work_time are missing - should cause validation errors
        ]);

        $response->assertStatus(302); // Redirect back with errors
        $response->assertSessionHasErrors(['travel_time', 'work_time']);
    }

    /**
     * Test that travel_time and work_time are required for High severity incidents when closing.
     */
    public function test_travel_and_work_time_required_for_high_severity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'High',
            'started_at' => now()->subHours(1),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(1)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'High',
            // travel_time and work_time are missing - should cause validation errors
        ]);

        $response->assertStatus(302); // Redirect back with errors
        $response->assertSessionHasErrors(['travel_time', 'work_time']);
    }

    /**
     * Test that travel_time and work_time are required for Critical severity incidents when closing.
     */
    public function test_travel_and_work_time_required_for_critical_severity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'Critical',
            'started_at' => now()->subHours(1),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(1)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'Critical',
            // travel_time and work_time are missing - should cause validation errors
        ]);

        $response->assertStatus(302); // Redirect back with errors
        $response->assertSessionHasErrors(['travel_time', 'work_time']);
    }

    /**
     * Test that travel_time and work_time are not required for Low severity incidents.
     */
    public function test_travel_and_work_time_not_required_for_low_severity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'Low',
            'started_at' => now()->subHours(2),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(2)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'Low',
            // travel_time and work_time not provided - should be OK
        ]);

        $response->assertRedirect(route('incidents.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test successful closing with all required fields for Medium severity + long duration.
     */
    public function test_successful_closing_with_all_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Use Medium severity which doesn't require RCA file
        $incident = Incident::factory()->create([
            'status' => 'Open',
            'severity' => 'Medium',
            'started_at' => now()->subHours(6),
            'resolved_at' => now(),
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->subHours(6)->format('Y-m-d H:i:s'),
            'resolved_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'Closed',
            'severity' => 'Medium',
            'delay_reason' => 'Equipment failure took longer than expected to diagnose',
            'travel_time' => 45,
            'work_time' => 120,
        ]);

        $response->assertRedirect(route('incidents.index'));
        $response->assertSessionHas('success');
        
        $incident->refresh();
        $this->assertEquals('Closed', $incident->status);
        $this->assertEquals(45, $incident->travel_time);
        $this->assertEquals(120, $incident->work_time);
    }
}
