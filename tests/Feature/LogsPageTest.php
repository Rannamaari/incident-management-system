<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test logs page displays incidents with pagination.
     */
    public function test_logs_page_displays_incidents(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create test incidents
        $incidents = Incident::factory(3)->create();

        $response = $this->get(route('logs.index'));

        $response->assertStatus(200)
                ->assertViewIs('logs.index')
                ->assertViewHas('incidents')
                ->assertSee('Incident Logs')
                ->assertSee('Export All Logs');

        // Check that incident codes are displayed
        foreach ($incidents as $incident) {
            $response->assertSee($incident->incident_code);
        }
    }

    /**
     * Test logs export functionality.
     */
    public function test_logs_export_all_incidents(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create test incidents
        $incident1 = Incident::factory()->create([
            'incident_code' => 'LOGS-TEST-1',
            'summary' => 'First test incident'
        ]);

        $incident2 = Incident::factory()->create([
            'incident_code' => 'LOGS-TEST-2', 
            'summary' => 'Second test incident'
        ]);

        $response = $this->get(route('logs.export'));

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Capture the CSV content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Check that both incidents are included in export
        $this->assertStringContainsString('LOGS-TEST-1', $content);
        $this->assertStringContainsString('LOGS-TEST-2', $content);
        $this->assertStringContainsString('First test incident', $content);
        $this->assertStringContainsString('Second test incident', $content);
    }

    /**
     * Test logs page requires authentication.
     */
    public function test_logs_page_requires_authentication(): void
    {
        $response = $this->get(route('logs.index'));
        
        $response->assertRedirect(route('login'));
    }

    /**
     * Test logs export requires authentication.
     */
    public function test_logs_export_requires_authentication(): void
    {
        $response = $this->get(route('logs.export'));
        
        $response->assertRedirect(route('login'));
    }

    /**
     * Test logs page pagination.
     */
    public function test_logs_page_pagination(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create more incidents than the per-page limit (25)
        Incident::factory(30)->create();

        $response = $this->get(route('logs.index'));

        $response->assertStatus(200);
        
        // Check pagination is present
        $incidents = $response->viewData('incidents');
        $this->assertEquals(25, $incidents->count());
        $this->assertEquals(30, $incidents->total());
    }

    /**
     * Test logs page date range filtering.
     */
    public function test_logs_page_date_filtering(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create incidents with specific dates
        $oldIncident = Incident::factory()->create([
            'incident_code' => 'OLD-2024-001',
            'started_at' => now()->subDays(30)
        ]);

        $recentIncident = Incident::factory()->create([
            'incident_code' => 'RECENT-2024-001', 
            'started_at' => now()->subDays(5)
        ]);

        $todayIncident = Incident::factory()->create([
            'incident_code' => 'TODAY-2024-001',
            'started_at' => now()
        ]);

        // Test with date_from filter
        $response = $this->get(route('logs.index', [
            'date_from' => now()->subDays(10)->format('Y-m-d')
        ]));

        $response->assertStatus(200)
                ->assertSee('RECENT-2024-001')
                ->assertSee('TODAY-2024-001')
                ->assertDontSee('OLD-2024-001');

        // Test with date_to filter
        $response = $this->get(route('logs.index', [
            'date_to' => now()->subDays(10)->format('Y-m-d')
        ]));

        $response->assertStatus(200)
                ->assertSee('OLD-2024-001')
                ->assertDontSee('RECENT-2024-001')
                ->assertDontSee('TODAY-2024-001');
    }

    /**
     * Test logs export with date range filtering.
     */
    public function test_logs_export_with_date_range(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create incidents with specific dates
        $oldIncident = Incident::factory()->create([
            'incident_code' => 'EXPORT-OLD-001',
            'started_at' => now()->subDays(30)
        ]);

        $recentIncident = Incident::factory()->create([
            'incident_code' => 'EXPORT-RECENT-001',
            'started_at' => now()->subDays(5)
        ]);

        // Export with date range filter
        $response = $this->get(route('logs.export', [
            'date_from' => now()->subDays(10)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d')
        ]));

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Check CSV content includes correct incident
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('EXPORT-RECENT-001', $content);
        $this->assertStringNotContainsString('EXPORT-OLD-001', $content);
    }
}