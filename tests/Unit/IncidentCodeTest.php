<?php

namespace Tests\Unit;

use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentCodeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test incident code is auto-generated on creation.
     */
    public function test_incident_code_is_auto_generated_on_creation(): void
    {
        $incident = Incident::create([
            'incident_id' => 'TEST-001',
            'summary' => 'Test incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => now(),
            'severity' => 'Medium',
        ]);

        $this->assertNotEmpty($incident->incident_code);
        
        $expectedDate = now()->format('Ymd');
        $this->assertStringStartsWith("INC-{$expectedDate}-", $incident->incident_code);
        $this->assertStringEndsWith('-0001', $incident->incident_code);
    }

    /**
     * Test incident codes increment per day.
     */
    public function test_incident_codes_increment_per_day(): void
    {
        $today = Carbon::parse('2024-01-15 10:00:00');
        Carbon::setTestNow($today);

        // Create first incident
        $incident1 = Incident::create([
            'incident_id' => 'TEST-001',
            'summary' => 'First incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $today,
            'severity' => 'Medium',
        ]);

        // Create second incident same day
        $incident2 = Incident::create([
            'incident_id' => 'TEST-002',
            'summary' => 'Second incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $today,
            'severity' => 'High',
        ]);

        // Create third incident same day
        $incident3 = Incident::create([
            'incident_id' => 'TEST-003',
            'summary' => 'Third incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $today,
            'severity' => 'Critical',
        ]);

        $this->assertEquals('INC-20240115-0001', $incident1->incident_code);
        $this->assertEquals('INC-20240115-0002', $incident2->incident_code);
        $this->assertEquals('INC-20240115-0003', $incident3->incident_code);
    }

    /**
     * Test incident codes reset for different days.
     */
    public function test_incident_codes_reset_for_different_days(): void
    {
        // Create incident on day 1
        $day1 = Carbon::parse('2024-01-15 10:00:00');
        $incident1 = Incident::create([
            'incident_id' => 'TEST-001',
            'summary' => 'Day 1 incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $day1,
            'severity' => 'Medium',
        ]);

        // Create incident on day 2
        $day2 = Carbon::parse('2024-01-16 14:30:00');
        $incident2 = Incident::create([
            'incident_id' => 'TEST-002',
            'summary' => 'Day 2 incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $day2,
            'severity' => 'High',
        ]);

        // Create second incident on day 2
        $incident3 = Incident::create([
            'incident_id' => 'TEST-003',
            'summary' => 'Day 2 second incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $day2,
            'severity' => 'Critical',
        ]);

        $this->assertEquals('INC-20240115-0001', $incident1->incident_code);
        $this->assertEquals('INC-20240116-0001', $incident2->incident_code);
        $this->assertEquals('INC-20240116-0002', $incident3->incident_code);
    }

    /**
     * Test incident code uses started_at date for generation.
     */
    public function test_incident_code_uses_started_at_date(): void
    {
        $specificDate = Carbon::parse('2024-03-20 15:45:00');
        
        $incident = Incident::create([
            'incident_id' => 'TEST-001',
            'summary' => 'Test incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $specificDate,
            'severity' => 'Medium',
        ]);

        $this->assertEquals('INC-20240320-0001', $incident->incident_code);
    }

    /**
     * Test incident code is unique.
     */
    public function test_incident_code_is_unique(): void
    {
        $today = now();

        $incident1 = Incident::create([
            'incident_id' => 'TEST-001',
            'summary' => 'First incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $today,
            'severity' => 'Medium',
        ]);

        $incident2 = Incident::create([
            'incident_id' => 'TEST-002',
            'summary' => 'Second incident',
            'outage_category' => 'Core Network',
            'category' => 'FBB',
            'affected_services' => 'Test Service',
            'started_at' => $today,
            'severity' => 'High',
        ]);

        $this->assertNotEquals($incident1->incident_code, $incident2->incident_code);
        
        // Verify both codes follow the pattern
        $pattern = '/^INC-\d{8}-\d{4}$/';
        $this->assertMatchesRegularExpression($pattern, $incident1->incident_code);
        $this->assertMatchesRegularExpression($pattern, $incident2->incident_code);
    }
}
