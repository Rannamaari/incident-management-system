<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Services\RCA\RcaGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncidentRCAController extends Controller
{
    protected $rcaGenerator;

    public function __construct(RcaGenerator $rcaGenerator)
    {
        $this->rcaGenerator = $rcaGenerator;
    }

    /**
     * Generate RCA document for the incident.
     */
    public function generate(Incident $incident)
    {
        // Only generate RCA for High/Critical incidents
        if (!in_array($incident->severity, ['High', 'Critical'])) {
            return back()->withErrors(['error' => 'RCA generation is only available for High and Critical severity incidents.']);
        }

        try {
            // Generate DOCX document
            $filename = $this->rcaGenerator->generateFromTemplate($incident);
            
            // Update incident with RCA file info
            $incident->update([
                'rca_file_path' => "rca/{$filename}",
                'rca_received_at' => now(),
            ]);

            return back()->with('success', 'RCA document generated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('RCA Generation Error: ' . $e->getMessage(), [
                'incident_id' => $incident->id,
                'incident_code' => $incident->incident_code,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to generate RCA document. Please try again.']);
        }
    }

    /**
     * Download RCA document.
     */
    public function download(Incident $incident)
    {
        if (!$incident->rca_file_path || !$incident->hasRcaFile()) {
            return back()->withErrors(['error' => 'RCA document not found.']);
        }

        $filename = "RCA_{$incident->incident_code}.docx";
        
        return Storage::download($incident->rca_file_path, $filename);
    }
}