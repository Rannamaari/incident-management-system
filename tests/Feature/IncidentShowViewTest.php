<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IncidentShowViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test incident show view displays correctly.
     */
    public function test_incident_show_view_displays_correctly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'incident_code' => 'INC-20250810-0001',
            'summary' => 'Test incident summary',
            'severity' => 'High',
            'status' => 'Open',
            'affected_services' => 'Test service',
            'category' => 'FBB',
            'outage_category' => 'Power',
        ]);

        // Add some logs
        IncidentLog::create([
            'incident_id' => $incident->id,
            'occurred_at' => now()->subHours(2),
            'note' => 'First log entry'
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'occurred_at' => now()->subHour(),
            'note' => 'Second log entry'
        ]);

        $response = $this->get(route('incidents.show', $incident));

        $response->assertStatus(200);
        $response->assertSee($incident->incident_code);
        $response->assertSee($incident->summary);
        $response->assertSee('High Priority');
        $response->assertSee('Test service');
        $response->assertSee('FBB');
        $response->assertSee('Power');
        $response->assertSee('First log entry');
        $response->assertSee('Second log entry');
        $response->assertSee('Edit Incident');
        $response->assertSee('Close Incident');
        $response->assertSee('Generate RCA'); // Should show for High severity
    }

    /**
     * Test RCA generation functionality.
     */
    public function test_rca_generation_for_high_severity_incident(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'severity' => 'Critical',
            'summary' => 'Critical incident',
            'root_cause' => 'Network failure',
        ]);

        $response = $this->post(route('incidents.generate-rca', $incident));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'RCA document generated successfully.');
        
        $incident->refresh();
        $this->assertNotNull($incident->rca_file_path);
        $this->assertNotNull($incident->rca_received_at);
        
        // Check that it's a DOCX file with correct naming
        $this->assertStringEndsWith('.docx', $incident->rca_file_path);
        $this->assertStringContainsString('rca/', $incident->rca_file_path);
        $this->assertStringContainsString($incident->incident_code, $incident->rca_file_path);
        
        // Check file exists using the incident model method
        $this->assertTrue($incident->hasRcaFile());
        
        // Clean up created file
        if ($incident->hasRcaFile()) {
            unlink(storage_path('app/' . $incident->rca_file_path));
        }
    }

    /**
     * Test RCA generation is blocked for low severity incidents.
     */
    public function test_rca_generation_blocked_for_low_severity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create(['severity' => 'Low']);

        $response = $this->post(route('incidents.generate-rca', $incident));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
    }

    /**
     * Test close incident modal shows conditional fields.
     */
    public function test_close_incident_modal_conditional_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'severity' => 'Medium',
            'status' => 'Open',
        ]);

        $response = $this->get(route('incidents.show', $incident));

        $response->assertStatus(200);
        // Should have the modal elements
        $response->assertSee('id="close-modal"', false);
        $response->assertSee('Travel Time (minutes)');
        $response->assertSee('Work Time (minutes)');
    }

    /**
     * Test closed incident doesn't show close button.
     */
    public function test_closed_incident_no_close_button(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create(['status' => 'Closed']);

        $response = $this->get(route('incidents.show', $incident));

        $response->assertStatus(200);
        $response->assertDontSee('id="close-incident-btn"', false);
    }

    /**
     * Test monthly KPI functionality on dashboard.
     */
    public function test_dashboard_monthly_kpi(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create incidents in current month
        Incident::factory()->create([
            'started_at' => now()->startOfMonth(),
            'status' => 'Open',
            'severity' => 'High'
        ]);

        Incident::factory()->create([
            'started_at' => now()->startOfMonth()->addDays(10),
            'status' => 'Closed',
            'severity' => 'Medium'
        ]);

        $response = $this->get(route('incidents.index'));

        $response->assertStatus(200)
                ->assertSee('View Month:')
                ->assertSee('input type="month"', false);

        // Check that monthly data is passed to view
        $monthlyIncidents = $response->viewData('monthlyIncidents');
        $selectedMonth = $response->viewData('selectedMonth');
        
        $this->assertNotNull($monthlyIncidents);
        $this->assertEquals(now()->format('Y-m'), $selectedMonth);
        $this->assertEquals(2, $monthlyIncidents->count());
    }
}
