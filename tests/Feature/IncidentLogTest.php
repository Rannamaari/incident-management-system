<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncidentLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating incident with logs.
     */
    public function test_create_incident_with_logs(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('incidents.store'), [
            'summary' => 'Test incident',
            'affected_services' => 'Test service',
            'started_at' => now()->format('Y-m-d H:i:s'),
            'resolved_at' => now()->addHour()->format('Y-m-d H:i:s'),
            'status' => 'Open',
            'severity' => 'Low',
            'outage_category' => 'Power',
            'category' => 'FBB',
            'logs' => [
                [
                    'occurred_at' => now()->format('Y-m-d H:i:s'),
                    'note' => 'First log entry'
                ],
                [
                    'occurred_at' => now()->addMinutes(30)->format('Y-m-d H:i:s'),
                    'note' => 'Second log entry'
                ]
            ]
        ]);

        $response->assertRedirect(route('incidents.index'));
        
        $incident = Incident::latest()->first();
        $this->assertCount(2, $incident->logs);
        $this->assertEquals('First log entry', $incident->logs->first()->note);
        $this->assertEquals('Second log entry', $incident->logs->last()->note);
    }

    /**
     * Test updating incident with logs.
     */
    public function test_update_incident_with_logs(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create incident with initial logs
        $incident = Incident::factory()->create([
            'severity' => 'Low', // Use Low severity to avoid travel/work time requirements
            'started_at' => now()->subHours(2),
            'resolved_at' => now(),
        ]);
        IncidentLog::create([
            'incident_id' => $incident->id,
            'occurred_at' => now()->subHours(2),
            'note' => 'Original log'
        ]);

        $response = $this->put(route('incidents.update', $incident), [
            'summary' => 'Updated incident',
            'affected_services' => 'Updated service',
            'started_at' => $incident->started_at->format('Y-m-d H:i:s'),
            'resolved_at' => $incident->resolved_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
            'status' => $incident->status,
            'severity' => 'Low', // Keep it Low to avoid validation rules
            'logs' => [
                [
                    'occurred_at' => now()->subHour()->format('Y-m-d H:i:s'),
                    'note' => 'New log entry 1'
                ],
                [
                    'occurred_at' => now()->format('Y-m-d H:i:s'),
                    'note' => 'New log entry 2'
                ]
            ]
        ]);

        $response->assertRedirect(route('incidents.index'));
        
        $incident->refresh();
        $this->assertCount(2, $incident->logs);
        $this->assertEquals('New log entry 1', $incident->logs->first()->note);
        $this->assertEquals('New log entry 2', $incident->logs->last()->note);
    }

    /**
     * Test logs are ordered by occurred_at asc.
     */
    public function test_logs_ordered_by_occurred_at(): void
    {
        $incident = Incident::factory()->create();
        
        // Create logs in random order
        IncidentLog::create([
            'incident_id' => $incident->id,
            'occurred_at' => now()->addHours(2),
            'note' => 'Third log'
        ]);
        
        IncidentLog::create([
            'incident_id' => $incident->id,
            'occurred_at' => now(),
            'note' => 'First log'
        ]);
        
        IncidentLog::create([
            'incident_id' => $incident->id,
            'occurred_at' => now()->addHour(),
            'note' => 'Second log'
        ]);

        $logs = $incident->logs;
        
        $this->assertEquals('First log', $logs->first()->note);
        $this->assertEquals('Second log', $logs->get(1)->note);
        $this->assertEquals('Third log', $logs->last()->note);
    }

    /**
     * Test creating incident with empty log entries filters them out.
     */
    public function test_create_incident_with_empty_logs_filtered(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('incidents.store'), [
            'summary' => 'Test incident with empty logs',
            'affected_services' => 'Test service',
            'started_at' => now()->format('Y-m-d H:i:s'),
            'resolved_at' => now()->addHour()->format('Y-m-d H:i:s'),
            'status' => 'Open',
            'severity' => 'Low',
            'outage_category' => 'Power',
            'category' => 'FBB',
            'logs' => [
                [
                    'occurred_at' => now()->format('Y-m-d H:i:s'),
                    'note' => 'Valid log entry'
                ],
                [
                    'occurred_at' => '',  // Empty - should be filtered out
                    'note' => ''         // Empty - should be filtered out
                ],
                [
                    'occurred_at' => now()->addMinutes(30)->format('Y-m-d H:i:s'),
                    'note' => 'Another valid log entry'
                ],
                [
                    'occurred_at' => '',           // Empty - should be filtered out
                    'note' => 'Note without date'  // Missing date - should be filtered out
                ]
            ]
        ]);

        $response->assertRedirect(route('incidents.index'));
        
        $incident = Incident::latest()->first();
        // Should only have 2 logs (the valid ones), empty entries filtered out
        $this->assertCount(2, $incident->logs);
        $this->assertEquals('Valid log entry', $incident->logs->first()->note);
        $this->assertEquals('Another valid log entry', $incident->logs->last()->note);
    }
}
