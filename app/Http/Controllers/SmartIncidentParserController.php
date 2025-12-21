<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Category;
use App\Models\OutageCategory;
use App\Models\FaultType;
use App\Models\ResolutionTeam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SmartIncidentParserController extends Controller
{
    /**
     * Show the smart incident parser form.
     */
    public function index()
    {
        return view('smart-parser.index');
    }

    /**
     * Parse the incident closure message and extract structured data.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'incident_message' => 'required|string|min:10'
        ]);

        $message = $request->input('incident_message');

        // Parse the message and extract details
        $parsedData = $this->extractIncidentDetails($message);

        // Get available options for dropdowns
        $categories = Category::orderBy('name')->get();
        $outageCategories = OutageCategory::orderBy('name')->get();
        $faultTypes = FaultType::orderBy('name')->get();
        $resolutionTeams = ResolutionTeam::orderBy('name')->get();

        // Convert category and outage_category names to IDs
        if (!empty($parsedData['category'])) {
            $category = $categories->firstWhere('name', $parsedData['category']);
            $parsedData['category_id'] = $category ? $category->id : null;
        }

        if (!empty($parsedData['outage_category'])) {
            $outageCategory = $outageCategories->firstWhere('name', $parsedData['outage_category']);
            $parsedData['outage_category_id'] = $outageCategory ? $outageCategory->id : null;
        }

        return view('smart-parser.review', compact('parsedData', 'categories', 'outageCategories', 'faultTypes', 'resolutionTeams', 'message'));
    }

    /**
     * Store the incident from parsed data.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'summary' => 'required|string|max:1000',
                'outage_category_id' => 'nullable|exists:outage_categories,id',
                'category_id' => 'nullable|exists:categories,id',
                'root_cause' => 'required|string',
                'started_at' => 'required|date',
                'resolved_at' => 'nullable|date', // Made nullable for open incidents
                'duration_minutes' => 'nullable|integer',
                'affected_services' => 'required|array',
                'affected_services.*' => 'string',
                'status' => 'required|string',
                'severity' => 'required|string',
                'fault_type_id' => 'nullable|exists:fault_types,id',
                'resolution_team_id' => 'nullable|exists:resolution_teams,id',
                'delay_reason' => 'nullable|string', // Required if duration > 5 hours (validated by model)
            ]);

            // Convert affected_services array to comma-separated string
            if (isset($validated['affected_services'])) {
                $affectedServicesArray = $validated['affected_services'];
                unset($validated['affected_services']);
                $validated['affected_services'] = implode(', ', $affectedServicesArray);
            }

            // Look up category and outage_category names from IDs
            // The database has BOTH text fields and ID fields that need to be populated
            if (!empty($validated['outage_category_id'])) {
                $outageCategory = OutageCategory::find($validated['outage_category_id']);
                $validated['outage_category'] = $outageCategory ? $outageCategory->name : null;
            }

            if (!empty($validated['category_id'])) {
                $category = Category::find($validated['category_id']);
                $validated['category'] = $category ? $category->name : null;
            }

            // Create the incident
            $incident = new Incident();
            $incident->fill($validated);
            $incident->created_by = auth()->id();
            $incident->updated_by = auth()->id();
            $incident->save();

            return redirect()->route('incidents.show', $incident)
                ->with('success', 'Incident created successfully from parsed message!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // If validation fails, redirect back to the parser index with errors
            return redirect()->route('smart-parser.index')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // If any other error occurs, redirect back with error message
            return redirect()->route('smart-parser.index')
                ->with('error', 'Failed to create incident: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Extract incident details from the closure message.
     */
    private function extractIncidentDetails(string $message): array
    {
        $data = [
            'summary' => '',
            'outage_category' => '',
            'category' => '',
            'root_cause' => '',
            'duration' => '',
            'duration_minutes' => null,
            'restoration_datetime' => '',
            'outage_start_datetime' => '',
            'affected_services' => [],
            'severity' => 'Low', // Always default to Low
            'status' => 'Open', // Default to Open
            'delay_reason_required' => false, // Will be set to true if duration > 5 hours
        ];

        // Detect incident status based on keywords
        // "on service", "restored", "up" = Closed
        // "down", "outage", "offline" = Open/In Progress
        if (preg_match('/\b(on service|restored|is up|back online|resolved)\b/i', $message)) {
            $data['status'] = 'Closed';
        } elseif (preg_match('/\b(down|outage|offline|not available)\b/i', $message)) {
            $data['status'] = 'Open';
        }

        // Extract restoration date and time for CLOSED incidents
        // Pattern: "on service since 1220hrs 21/12/2025" or "on service since 1042hrs 21/12/2025"
        if (preg_match('/on service since (\d{4})hrs (\d{2}\/\d{2}\/\d{4})/i', $message, $matches)) {
            $timeStr = $matches[1]; // e.g., "1220"
            $dateStr = $matches[2]; // e.g., "21/12/2025"

            $hours = substr($timeStr, 0, 2);
            $minutes = substr($timeStr, 2, 2);

            try {
                $restorationDate = Carbon::createFromFormat('d/m/Y H:i', "$dateStr $hours:$minutes");
                $data['restoration_datetime'] = $restorationDate->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, leave empty
            }
        }

        // Extract start date and time for OPEN incidents
        // Pattern: "down since 1430hrs 21/12/2025" or "offline since 1200hrs 20/12/2025"
        if (preg_match('/(down|offline|outage)\s+since\s+(\d{4})hrs\s+(\d{2}\/\d{2}\/\d{4})/i', $message, $matches)) {
            $timeStr = $matches[2]; // e.g., "1430"
            $dateStr = $matches[3]; // e.g., "21/12/2025"

            $hours = substr($timeStr, 0, 2);
            $minutes = substr($timeStr, 2, 2);

            try {
                $startDate = Carbon::createFromFormat('d/m/Y H:i', "$dateStr $hours:$minutes");
                $data['outage_start_datetime'] = $startDate->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, leave empty
            }
        }

        // Extract duration
        // Pattern: "Duration: 30mins" or "Duration: 2hrs 12mins" or "Duration: 1hr 43mins"
        if (preg_match('/Duration:\s*(\d+\s*hrs?\s*)?(\d+)?\s*mins?/i', $message, $matches)) {
            $data['duration'] = trim($matches[0]);

            $hours = 0;
            $minutes = 0;

            if (preg_match('/(\d+)\s*hrs?/i', $data['duration'], $hourMatch)) {
                $hours = (int)$hourMatch[1];
            }

            if (preg_match('/(\d+)\s*mins?/i', $data['duration'], $minMatch)) {
                $minutes = (int)$minMatch[1];
            }

            $data['duration_minutes'] = ($hours * 60) + $minutes;

            // Check if delay reason is required (duration > 5 hours = 300 minutes)
            if ($data['duration_minutes'] > 300) {
                $data['delay_reason_required'] = true;
            }

            // Calculate outage start time
            if (!empty($data['restoration_datetime']) && $data['duration_minutes']) {
                try {
                    $restorationDate = Carbon::parse($data['restoration_datetime']);
                    $outageStart = $restorationDate->copy()->subMinutes($data['duration_minutes']);
                    $data['outage_start_datetime'] = $outageStart->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    // If parsing fails, leave empty
                }
            }
        }

        // Extract root cause
        // Pattern: "Cause: Local power failure." or "Cause: Under investigation. Cells came on service after Resort IT gave power reset to RRU."
        if (preg_match('/Cause:\s*(.+?)(?=\n\n|$)/is', $message, $matches)) {
            $data['root_cause'] = trim($matches[1]);
            // Remove any trailing slashes or extra whitespace
            $data['root_cause'] = rtrim($data['root_cause'], "\\ \t\n\r\0\x0B");
        }

        // Detect if it's FBB based on message content
        $isFBB = preg_match('/\bFBB\b/i', $message);

        // Check if it's a Single Site (5G, site, tower mentions)
        $isSingleSite = preg_match('/\b(5G|site|tower)\b/i', $message) && !preg_match('/cells are on service/i', $message);

        // Extract all cell/site names using improved pattern
        // Matches patterns like: K_Hulhumale_TreeTop_ATM_AAU_L1800A,B or Male_Hulhumale_MTCC_Terminal_AAU_U2100-2774A,B
        preg_match_all('/([A-Z]{1,4}_[A-Za-z0-9_]+(?:AAU|RRU|Resort|MTCC|Terminal|TreeTop|Pole\d*)?[_-]?[A-Z]*\d+[-_]?\d*[A-Z]*,?[A-B]*)/i', $message, $cellMatches);

        // Clean up the matches
        $cells = array_unique($cellMatches[0]);
        $cells = array_filter($cells, function($cell) {
            // Filter out very short matches that might be false positives
            return strlen($cell) > 5;
        });

        $cellCount = count($cells);

        // Check for service type keywords
        $isISPLink = preg_match('/\b(ISP|international link|IX|peering)\b/i', $message);
        $isLeaseCircuit = preg_match('/\b(lease circuit|lease line|leased line|P2P|point to point)\b/i', $message);

        // Determine outage type and affected services
        if ($isFBB && $cellCount <= 1) {
            // Single FBB outage
            if (preg_match('/([A-Z]{2,4}_[A-Za-z]+)\s+FBB/i', $message, $matches)) {
                $data['summary'] = $matches[1] . ' FBB';
                $data['affected_services'] = ['Single FBB'];
                $data['category'] = 'FBB'; // Service affected is FBB
            }
        } elseif ($isSingleSite && $cellCount <= 1) {
            // Single Site outage (like 5G site)
            // Extract site name
            if (preg_match('/([A-Z]{2,4}_[A-Za-z]+)\s+(5G|site)/i', $message, $matches)) {
                $data['summary'] = $matches[1] . ' ' . $matches[2];
            } else {
                $data['summary'] = trim(explode("\n", $message)[0]);
            }
            $data['outage_category'] = 'RAN'; // Infrastructure type
            $data['affected_services'] = ['Single Site'];
            $data['category'] = 'RAN'; // Service affected
        } elseif ($cellCount > 0) {
            // RAN outage with cells (AAU, cell tower, mobile tower, etc.)
            $data['outage_category'] = 'RAN'; // Infrastructure type
            $data['affected_services'] = ['Cell'];
            $data['category'] = 'RAN'; // Service affected

            // Extract all cell names and format them nicely
            $data['summary'] = implode("\n", $cells);
        } elseif ($isISPLink) {
            // ISP/International link
            $data['outage_category'] = 'International';
            $data['category'] = 'International';
            $data['affected_services'] = ['ILL'];
        } elseif ($isLeaseCircuit) {
            // Lease circuit/line
            $data['outage_category'] = 'Enterprise';
            $data['category'] = 'Enterprise';
            $data['affected_services'] = ['P2P'];
        } else {
            // Fallback: try to extract the first line as summary
            $lines = explode("\n", $message);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !preg_match('/^(Duration:|Cause:)/i', $line)) {
                    $data['summary'] = $line;
                    break;
                }
            }
        }

        // Detect OUTAGE CATEGORY based on root cause
        // Only override to Power if it's explicitly a power FAILURE/OUTAGE (not just mentioning power in the solution)
        $isPowerFailure = preg_match('/\b(power failure|power outage|local power|DCDU.*tripped|breaker.*tripped)\b/i', $data['root_cause']);
        $isTransmissionIssue = preg_match('/\b(fiber cut|cable cut|transmission)\b/i', $data['root_cause']);

        if ($isPowerFailure) {
            // Explicitly a power failure - override to Power
            $data['outage_category'] = 'Power';
        } elseif ($isTransmissionIssue) {
            // Transmission issue
            $data['outage_category'] = 'Transmission';
        }
        // Otherwise keep the infrastructure-based outage category (RAN, International, Enterprise, etc.)
        // that was already set above based on the type of outage

        return $data;
    }
}
