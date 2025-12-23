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

        // ========== AI-FIRST PARSING APPROACH ==========
        // Try comprehensive AI extraction first for natural language understanding
        $aiService = new \App\Services\AIIncidentParserService();
        $aiData = $aiService->comprehensiveExtraction($message);

        // Fallback to regex parsing if AI fails
        $regexData = $this->extractIncidentDetails($message);

        // Merge AI and regex results (AI takes priority, regex fills gaps)
        $parsedData = $this->mergeParsingResults($aiData, $regexData);

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
     * Intelligently merge AI and regex parsing results.
     * AI results take priority, regex fills in gaps.
     *
     * @param array|null $aiData Results from AI extraction
     * @param array $regexData Results from regex extraction
     * @return array Merged results
     */
    private function mergeParsingResults(?array $aiData, array $regexData): array
    {
        // If AI failed completely, use regex
        if (empty($aiData)) {
            return $regexData;
        }

        // Start with regex data as base (has all fields)
        $merged = $regexData;

        // Override with AI data where AI provided better results
        foreach ($aiData as $key => $value) {
            // Skip null/empty AI values unless regex also has nothing
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            // AI wins for these critical fields
            if (in_array($key, ['summary', 'status', 'root_cause', 'outage_category', 'category', 'affected_services'])) {
                $merged[$key] = $value;
            }

            // For date/time fields, prefer REGEX over AI (regex is more accurate for exact times)
            // Only use AI datetime if regex didn't extract it
            if (in_array($key, ['started_at', 'resolved_at']) && $this->isValidDateTime($value)) {
                // Map AI fields to regex field names
                if ($key === 'started_at' && empty($merged['outage_start_datetime'])) {
                    $merged['outage_start_datetime'] = $value;
                } elseif ($key === 'resolved_at' && empty($merged['restoration_datetime'])) {
                    $merged['restoration_datetime'] = $value;
                }
            }

            // For duration, prefer REGEX over AI (regex is more accurate for explicit duration)
            // Only use AI duration if regex didn't extract it
            if ($key === 'duration_minutes' && is_numeric($value) && $value > 0 && empty($merged['duration_minutes'])) {
                $merged['duration_minutes'] = $value;

                // Format human-readable duration with days support
                $days = floor($value / (24 * 60));
                $remainingMins = $value % (24 * 60);
                $hours = floor($remainingMins / 60);
                $mins = $remainingMins % 60;

                if ($days > 0) {
                    $merged['duration'] = $days . ' days ' . $hours . 'hrs ' . $mins . 'mins';
                } elseif ($hours > 0) {
                    $merged['duration'] = $hours . 'hrs ' . $mins . 'mins';
                } else {
                    $merged['duration'] = $mins . 'mins';
                }

                // Check if delay reason is required
                if ($value > 300) {
                    $merged['delay_reason_required'] = true;
                }
            }

            // For delay_reason, prefer AI extraction
            if ($key === 'delay_reason' && !empty($value)) {
                $merged['delay_reason'] = $value;
            }

            // For severity, use AI if provided
            if ($key === 'severity' && !empty($value)) {
                $merged['severity'] = $value;
            }
        }

        // Add marker that AI was used
        $merged['ai_enhanced'] = true;

        // Track which fields came from which source (for transparency)
        $merged['field_sources'] = [
            'summary' => isset($aiData['summary']) && !empty($aiData['summary']) ? 'AI' : 'Regex',
            'status' => isset($aiData['status']) && !empty($aiData['status']) ? 'AI' : 'Regex',
            'outage_start_datetime' => !empty($regexData['outage_start_datetime']) ? 'Regex' : 'AI',
            'restoration_datetime' => !empty($regexData['restoration_datetime']) ? 'Regex' : 'AI',
            'duration_minutes' => isset($aiData['duration_minutes']) && $aiData['duration_minutes'] > 0 ? 'AI' : 'Regex',
            'root_cause' => isset($aiData['root_cause']) && !empty($aiData['root_cause']) ? 'AI' : 'Regex',
            'outage_category' => isset($aiData['outage_category']) && !empty($aiData['outage_category']) ? 'AI' : 'Regex',
            'affected_services' => isset($aiData['affected_services']) && !empty($aiData['affected_services']) ? 'AI' : 'Regex',
            'delay_reason' => isset($aiData['delay_reason']) && !empty($aiData['delay_reason']) ? 'AI' : 'Regex',
        ];

        return $merged;
    }

    /**
     * Check if a string is a valid datetime.
     *
     * @param mixed $datetime
     * @return bool
     */
    private function isValidDateTime($datetime): bool
    {
        if (!is_string($datetime)) {
            return false;
        }

        try {
            \Carbon\Carbon::parse($datetime);
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
                'sites_2g_impacted' => 'nullable|integer|min:0',
                'sites_3g_impacted' => 'nullable|integer|min:0',
                'sites_4g_impacted' => 'nullable|integer|min:0',
                'sites_5g_impacted' => 'nullable|integer|min:0',
                'fbb_impacted' => 'nullable|integer|min:0',
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

            // Check for duplicate incident (same summary at any time)
            // Allow user to bypass this check by setting 'confirm_duplicate' field
            if (!$request->input('confirm_duplicate')) {
                $duplicate = Incident::where('summary', $validated['summary'])
                    ->with('creator')
                    ->first();

                if ($duplicate) {
                    // Get all form data to pass back to review view
                    $categories = Category::orderBy('name')->get();
                    $outageCategories = OutageCategory::orderBy('name')->get();
                    $faultTypes = FaultType::orderBy('name')->get();
                    $resolutionTeams = ResolutionTeam::orderBy('name')->get();

                    // Prepare parsed data from validated request data
                    $parsedData = $validated;

                    // Restore affected_services array format for the view
                    if (isset($parsedData['affected_services']) && is_string($parsedData['affected_services'])) {
                        $parsedData['affected_services'] = array_filter(array_map('trim', explode(',', $parsedData['affected_services'])));
                    }

                    // Get original message from request if available
                    $message = $request->input('original_message', 'Message not available');

                    // Return to review view with error
                    return view('smart-parser.review', compact('parsedData', 'categories', 'outageCategories', 'faultTypes', 'resolutionTeams', 'message'))
                        ->withErrors([
                            'duplicate' => 'A similar incident already exists: "' . $duplicate->summary . '" created by ' .
                                ($duplicate->creator ? $duplicate->creator->name : 'Unknown') . ' on ' .
                                $duplicate->created_at->format('M d, Y H:i') . ' (started at: ' . $duplicate->started_at->format('M d, Y H:i') . '). If you are sure you want to create this duplicate incident, please click "Create Anyway" below.'
                        ]);
                }
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
        if (preg_match('/\b(on service|stable|is up|back online|restored|resolved)\b/i', $message)) {
            $data['status'] = 'Closed';
        } elseif (preg_match('/\b(down|off|unstable|outage|offline|not available)\b/i', $message)) {
            $data['status'] = 'Open';
        }

        // Extract restoration date and time for CLOSED incidents
        // Pattern: "on service since", "stable since", "up since" + time + date
        if (preg_match('/(on service|stable|up) since (\d{4})hrs\.?\s*\(?\s*(\d{1,2}\/\d{1,2}\/\d{4})\)?/i', $message, $matches)) {
            $timeStr = $matches[2]; // e.g., "1220" or "1531"
            $dateStr = $matches[3]; // e.g., "21/12/2025" or "26/11/2025"

            $hours = substr($timeStr, 0, 2);
            $minutes = substr($timeStr, 2, 2);

            try {
                $restorationDate = Carbon::createFromFormat('j/n/Y H:i', "$dateStr $hours:$minutes");
                $data['restoration_datetime'] = $restorationDate->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, leave empty
            }
        }

        // Extract start date and time for OPEN incidents
        // Pattern: "down since", "off since", "unstable since" + time + date
        if (preg_match('/(down|off|unstable|offline|outage)\s+since\s+(\d{4})hrs\s+(\d{2}\/\d{2}\/\d{4})/i', $message, $matches)) {
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

        // Check for "total down duration" which indicates manual entry required
        $hasTotalDownDuration = preg_match('/\btotal\s+down\s+duration\b/i', $message) ||
                                preg_match('/\bTDD\b/', $message);

        // Extract duration
        // Pattern: "Duration: 30mins" or "Duration: 2hrs 12mins" or "Duration: 3 days 4hrs 46mins"
        // SKIP auto-calculation if "total down duration" is mentioned
        if (!$hasTotalDownDuration && preg_match('/Duration:\s*(\d+\s*days?\s*)?(\d+\s*hrs?\s*)?(\d+)?\s*mins?/i', $message, $matches)) {
            $data['duration'] = trim($matches[0]);

            $days = 0;
            $hours = 0;
            $minutes = 0;

            if (preg_match('/(\d+)\s*days?/i', $data['duration'], $dayMatch)) {
                $days = (int)$dayMatch[1];
            }

            if (preg_match('/(\d+)\s*hrs?/i', $data['duration'], $hourMatch)) {
                $hours = (int)$hourMatch[1];
            }

            if (preg_match('/(\d+)\s*mins?/i', $data['duration'], $minMatch)) {
                $minutes = (int)$minMatch[1];
            }

            $data['duration_minutes'] = ($days * 24 * 60) + ($hours * 60) + $minutes;

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
        if (preg_match('/Cause:\s*(.+?)(?=\nNote:|$)/is', $message, $matches)) {
            $data['root_cause'] = trim($matches[1]);
            // Remove any trailing slashes or extra whitespace
            $data['root_cause'] = rtrim($data['root_cause'], "\\ \t\n\r\0\x0B");
        }

        // Extract Note field (often contains delay reason for long outages)
        // Pattern: "Note: Restoration was delayed due to bad weather..."
        if (preg_match('/Note:\s*(.+?)(?=\n\n|$)/is', $message, $matches)) {
            $data['note'] = trim($matches[1]);

            // If duration > 5 hours and there's a Note, automatically use it as delay_reason
            if (isset($data['duration_minutes']) && $data['duration_minutes'] > 300 && !empty($data['note'])) {
                $data['delay_reason'] = $data['note'];
                // Keep delay_reason_required flag for UI consistency
                $data['delay_reason_required'] = true;
            }
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
            if (preg_match('/([A-Z]{1,4}_[A-Za-z]+)\s+FBB/i', $message, $matches)) {
                $data['summary'] = $matches[1] . ' FBB';
                $data['affected_services'] = ['Single FBB'];
                $data['category'] = 'FBB'; // Service affected is FBB
            }
        } elseif ($isSingleSite && $cellCount <= 1) {
            // Single Site outage (like 5G site)
            // Extract site name
            if (preg_match('/([A-Z]{1,4}_[A-Za-z]+)\s+(5G|site|3G\/4G|4G|LTE)/i', $message, $matches)) {
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

            // IMPORTANT: Only use "Multiple Site" if message explicitly mentions "sites" (plural)
            // Otherwise, multiple cells should be categorized as "Cell"
            $hasSitesKeyword = preg_match('/\bsites\b/i', $message);

            if ($hasSitesKeyword) {
                $data['affected_services'] = ['Multiple Site'];
            } else {
                $data['affected_services'] = ['Cell'];
            }

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

        // ========== AI-POWERED ENHANCEMENT ==========
        // Use AI to extract summary if regex failed or produced low-quality results
        $summaryNeedsAI = empty($data['summary']) ||
                          strlen($data['summary']) > 200 || // Too long - probably extracted full sentence
                          preg_match('/\b(is on service|is down|since|duration|cause)\b/i', $data['summary']); // Contains unwanted keywords

        if ($summaryNeedsAI) {
            try {
                $aiService = new \App\Services\AIIncidentParserService();
                $aiSummary = $aiService->extractSummary($message);

                if (!empty($aiSummary)) {
                    $data['summary'] = $aiSummary;
                    $data['ai_extracted_summary'] = true; // Flag for debugging
                }
            } catch (\Exception $e) {
                // If AI fails, keep the regex-extracted summary (or empty)
                \Log::warning('AI Summary Extraction Failed', [
                    'error' => $e->getMessage(),
                    'message_preview' => substr($message, 0, 100),
                ]);
            }
        }

        return $data;
    }
}
