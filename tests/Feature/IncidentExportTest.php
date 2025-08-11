<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test CSV export returns proper headers and content.
     */
    public function test_csv_export_returns_proper_response(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'incident_code' => 'TEST-EXPORT-001',
            'summary' => 'Test export incident',
            'category' => 'FBB',
            'severity' => 'High',
            'status' => 'Closed'
        ]);

        $response = $this->get(route('incidents.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $contentDisposition);
        $this->assertStringContainsString('.csv', $contentDisposition);
    }

    /**
     * Test CSV export with filters.
     */
    public function test_csv_export_with_filters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create incidents with specific criteria
        $incident1 = Incident::factory()->create([
            'incident_code' => 'FILTER-001',
            'category' => 'FBB',
            'status' => 'Closed',
            'started_at' => now()->subDays(1)
        ]);

        $incident2 = Incident::factory()->create([
            'incident_code' => 'FILTER-002',
            'category' => 'RAN',
            'status' => 'Open',
            'started_at' => now()->subDays(2)
        ]);

        // Test with category filter
        $response = $this->get(route('incidents.export', ['category' => 'FBB']));
        $response->assertStatus(200);

        // Test with status filter
        $response = $this->get(route('incidents.export', ['status' => 'Closed']));
        $response->assertStatus(200);

        // Test with date range filter
        $response = $this->get(route('incidents.export', [
            'date_from' => now()->subDays(2)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d')
        ]));
        $response->assertStatus(200);
    }

    /**
     * Test CSV export validation.
     */
    public function test_csv_export_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test invalid date range
        $response = $this->get(route('incidents.export', [
            'date_from' => '2025-01-10',
            'date_to' => '2025-01-05'  // End date before start date
        ]));

        $response->assertStatus(302); // Redirect due to validation error
        $response->assertSessionHasErrors(['date_to']);
    }

    /**
     * Test CSV headers include all expected columns.
     */
    public function test_csv_includes_all_expected_columns(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident = Incident::factory()->create([
            'summary' => 'Test CSV columns',
            'category' => 'FBB',
            'outage_category' => 'Power',
            'severity' => 'Medium'
        ]);

        $response = $this->get(route('incidents.export'));
        
        // For streaming responses, we need to capture the streamed content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        // Check for essential CSV headers
        $this->assertStringContainsString('Incident Code', $content);
        $this->assertStringContainsString('Summary', $content);
        $this->assertStringContainsString('Category', $content);
        $this->assertStringContainsString('Outage Category', $content);
        $this->assertStringContainsString('Fault Type', $content);
        $this->assertStringContainsString('Severity', $content);
        $this->assertStringContainsString('Started At', $content);
        $this->assertStringContainsString('Resolved At', $content);
        $this->assertStringContainsString('Duration (Minutes)', $content);
        $this->assertStringContainsString('Resolution Team', $content);
    }

    /**
     * Test export requires authentication.
     */
    public function test_csv_export_requires_authentication(): void
    {
        $response = $this->get(route('incidents.export'));
        
        $response->assertRedirect(route('login'));
    }

    /**
     * Test export preview endpoint returns JSON data.
     */
    public function test_export_preview_returns_json(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create test incidents
        $incident1 = Incident::factory()->create([
            'incident_code' => 'PREVIEW-001',
            'category' => 'FBB',
            'status' => 'Open',
            'severity' => 'High'
        ]);

        $incident2 = Incident::factory()->create([
            'incident_code' => 'PREVIEW-002',
            'category' => 'RAN',
            'status' => 'Closed',
            'severity' => 'Medium'
        ]);

        $response = $this->getJson(route('incidents.export.preview'));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'total',
                    'preview' => [
                        '*' => [
                            'incident_code',
                            'summary',
                            'category',
                            'status',
                            'severity',
                            'started_at',
                            'resolved_at'
                        ]
                    ]
                ])
                ->assertJson([
                    'total' => 2
                ]);
    }

    /**
     * Test export preview with filters.
     */
    public function test_export_preview_with_filters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $incident1 = Incident::factory()->create([
            'category' => 'FBB',
            'status' => 'Open'
        ]);

        $incident2 = Incident::factory()->create([
            'category' => 'RAN',
            'status' => 'Closed'
        ]);

        // Test category filter
        $response = $this->getJson(route('incidents.export.preview', ['category' => 'FBB']));
        
        $response->assertStatus(200)
                ->assertJson(['total' => 1]);

        // Test status filter
        $response = $this->getJson(route('incidents.export.preview', ['status' => 'Closed']));
        
        $response->assertStatus(200)
                ->assertJson(['total' => 1]);
    }

    /**
     * Test CSV export with date range filters.
     */
    public function test_csv_export_with_date_range(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create incidents with specific dates
        $incident1 = Incident::factory()->create([
            'incident_code' => 'DATE-TEST-1',
            'started_at' => '2024-01-15 10:00:00',
            'summary' => 'January incident'
        ]);

        $incident2 = Incident::factory()->create([
            'incident_code' => 'DATE-TEST-2',
            'started_at' => '2024-02-15 10:00:00',
            'summary' => 'February incident'
        ]);

        $incident3 = Incident::factory()->create([
            'incident_code' => 'DATE-TEST-3',
            'started_at' => '2024-03-15 10:00:00',
            'summary' => 'March incident'
        ]);

        // Export with date range that should only include February incident
        $response = $this->get(route('incidents.export', [
            'date_from' => '2024-02-01',
            'date_to' => '2024-02-28'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        // Capture the CSV content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        // Check that only February incident is included
        $this->assertStringContainsString('DATE-TEST-2', $content);
        $this->assertStringContainsString('February incident', $content);
        $this->assertStringNotContainsString('DATE-TEST-1', $content);
        $this->assertStringNotContainsString('DATE-TEST-3', $content);
        $this->assertStringNotContainsString('January incident', $content);
        $this->assertStringNotContainsString('March incident', $content);
    }
}