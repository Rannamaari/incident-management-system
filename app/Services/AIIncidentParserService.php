<?php

namespace App\Services;

use Anthropic\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class AIIncidentParserService
{
    protected $client;
    protected $model = 'claude-3-haiku-20240307'; // Claude 3 Haiku - fastest and cheapest

    public function __construct()
    {
        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new Exception('Anthropic API key not configured. Please add ANTHROPIC_API_KEY to your .env file.');
        }

        $this->client = new Client(
            apiKey: $apiKey
        );
    }

    /**
     * Extract incident summary from a message using AI.
     * This is called when regex parsing fails to identify the summary.
     *
     * @param string $message The incident closure message
     * @return string|null The extracted summary or null if extraction fails
     */
    public function extractSummary(string $message): ?string
    {
        try {
            $prompt = $this->buildSummaryExtractionPrompt($message);

            $response = $this->client->messages->create([
                'model' => $this->model,
                'max_tokens' => 100, // Short response needed
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            $extractedSummary = trim($response->content[0]->text);

            Log::info('AI Summary Extraction', [
                'input' => substr($message, 0, 200),
                'output' => $extractedSummary,
                'tokens_used' => $response->usage->input_tokens + $response->usage->output_tokens,
            ]);

            return $extractedSummary;

        } catch (Exception $e) {
            Log::error('AI Summary Extraction Failed', [
                'error' => $e->getMessage(),
                'message' => substr($message, 0, 200),
            ]);

            return null; // Fallback to regex or empty
        }
    }

    /**
     * Verify or determine the outage category using AI.
     * This helps when the category is ambiguous (e.g., power vs RAN).
     *
     * @param string $message The full incident message
     * @param string $rootCause The extracted root cause
     * @return string|null The outage category or null if determination fails
     */
    public function determineOutageCategory(string $message, string $rootCause): ?string
    {
        try {
            $prompt = $this->buildCategoryDeterminationPrompt($message, $rootCause);

            $response = $this->client->messages->create([
                'model' => $this->model,
                'max_tokens' => 50, // Very short response
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            $category = trim($response->content[0]->text);

            // Validate that the category is one of the expected values
            $validCategories = ['Power', 'RAN', 'Transmission', 'International', 'Enterprise', 'FBB'];
            if (in_array($category, $validCategories)) {
                Log::info('AI Category Determination', [
                    'root_cause' => $rootCause,
                    'determined_category' => $category,
                    'tokens_used' => $response->usage->input_tokens + $response->usage->output_tokens,
                ]);

                return $category;
            }

            return null;

        } catch (Exception $e) {
            Log::error('AI Category Determination Failed', [
                'error' => $e->getMessage(),
                'root_cause' => $rootCause,
            ]);

            return null;
        }
    }

    /**
     * Build the prompt for summary extraction.
     *
     * @param string $message The incident message
     * @return string The formatted prompt
     */
    private function buildSummaryExtractionPrompt(string $message): string
    {
        return <<<PROMPT
Extract ONLY the service/site identifier from this incident closure message. Return just the identifier, nothing else.

Rules:
- Include the location/site name and technology type (e.g., "3G/4G", "5G", "FBB", "LTE")
- Examples of correct outputs:
  * "Dh_Kandima_Resort 3G/4G"
  * "GA_Kondey FBB"
  * "GDh_Thinadhoo 5G"
  * "K_Hulhumale_TreeTop_ATM_AAU_L1800A,B"
- Do NOT include "is on service", "is down", or any other descriptive text
- Do NOT include dates, times, or cause information
- If multiple cells are listed, include all of them (one per line)

Incident Message:
{$message}

Return ONLY the service/site identifier:
PROMPT;
    }

    /**
     * Build the prompt for outage category determination.
     *
     * @param string $message The full message
     * @param string $rootCause The root cause
     * @return string The formatted prompt
     */
    private function buildCategoryDeterminationPrompt(string $message, string $rootCause): string
    {
        return <<<PROMPT
Determine the outage category based on the root cause. Return ONLY ONE of these exact words: Power, RAN, Transmission, International, Enterprise, FBB

Guidelines:
- "Power" = Power failure, power outage, DCDU tripped, breaker tripped, battery issues
- "RAN" = Radio Access Network issues, cell tower, site issues, equipment failure (AAU, RRU)
- "Transmission" = Fiber cut, cable cut, transmission link issues
- "International" = International link, IX, peering issues
- "Enterprise" = Lease circuit, P2P, enterprise services
- "FBB" = Fixed broadband specific issues

IMPORTANT: If power is mentioned as a SOLUTION (e.g., "gave power reset", "restored power"), but NOT the cause, it's probably RAN, not Power.

Root Cause: {$rootCause}

Full Message Context: {$message}

Return ONLY the category name:
PROMPT;
    }

    /**
     * Comprehensive AI extraction of ALL incident fields.
     * Use this as a fallback when regex parsing produces incomplete or poor results.
     *
     * @param string $message The full incident message
     * @return array|null Extracted data or null if extraction fails
     */
    public function comprehensiveExtraction(string $message): ?array
    {
        try {
            $prompt = $this->buildComprehensiveExtractionPrompt($message);

            $response = $this->client->messages->create([
                'model' => $this->model,
                'max_tokens' => 500, // Longer response for comprehensive extraction
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            $jsonResponse = trim($response->content[0]->text);

            // Parse JSON response
            $extracted = json_decode($jsonResponse, true);

            if ($extracted && is_array($extracted)) {
                Log::info('AI Comprehensive Extraction', [
                    'input' => substr($message, 0, 200),
                    'output' => $extracted,
                    'tokens_used' => $response->usage->input_tokens + $response->usage->output_tokens,
                ]);

                return $extracted;
            }

            return null;

        } catch (Exception $e) {
            Log::error('AI Comprehensive Extraction Failed', [
                'error' => $e->getMessage(),
                'message' => substr($message, 0, 200),
            ]);

            return null;
        }
    }

    /**
     * Build the prompt for comprehensive extraction.
     *
     * @param string $message The incident message
     * @return string The formatted prompt
     */
    private function buildComprehensiveExtractionPrompt(string $message): string
    {
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');

        return <<<PROMPT
You are an intelligent incident parser. Extract ALL relevant fields from this incident message written in natural English and return them as JSON.

TODAY'S DATE: {$today}
CURRENT TIME: {$currentTime}

Message:
{$message}

Extract these fields (return null for any field that cannot be determined):
{
  "summary": "Service/site name with technology (e.g., 'L_Hithadhoo FBB', 'Dh_Kandima_Resort 3G/4G')",
  "status": "Open or Closed (down/offline/not working = Open, on service/restored/back online = Closed)",
  "started_at": "When the OUTAGE STARTED (service went DOWN). CRITICAL: If 'on service since 16:22, Duration 8:37', then started_at = 16:22 MINUS 8:37 = 07:45",
  "resolved_at": "When service was RESTORED (came back UP). CRITICAL: 'on service since 16:22' means resolved_at = 16:22 (NOT started_at!)",
  "duration_minutes": "Total outage duration in minutes (calculate if not explicitly stated)",
  "root_cause": "What caused the outage (exclude Note content)",
  "delay_reason": "Why restoration took long (extract from 'Note:' if duration > 5 hours)",
  "affected_services": ["Array of affected services"],
  "outage_category": "One of: Power, RAN, Transmission, International, Enterprise, FBB",
  "category": "Same as outage_category usually",
  "severity": "Low, Medium, or High (default to Low if not specified)"
}

EXAMPLES FOR CLARITY:

Example 1 - With date:
Input: "GA_Kondey FBB is on service since 1220hrs 21/12/2025, Duration: 30mins"
Output: {
  "started_at": "2025-12-21 11:50",  ← Restoration time MINUS duration (12:20 - 30min = 11:50)
  "resolved_at": "2025-12-21 12:20", ← "on service since" = restoration/resolved time
  "duration_minutes": 30
}

Example 2 - Without date (use TODAY):
Input: "Below mentioned cell is on service since 1622hrs. Duration: 8hrs 37mins"
Output: {
  "started_at": "{TODAY} 07:45",    ← Restoration time MINUS duration (16:22 - 8:37 = 07:45)
  "resolved_at": "{TODAY} 16:22",   ← "on service since" = restoration/resolved time
  "duration_minutes": 517             ← 8*60 + 37 = 517 minutes
}

Example 3 - Multiple cells with full date:
Input: "Below mentioned cells are on service since 1909hrs 20/12/2025 Duration: 7hrs 10mins"
Output: {
  "started_at": "2025-12-20 11:59",  ← Restoration time MINUS duration (19:09 - 7:10 = 11:59)
  "resolved_at": "2025-12-20 19:09", ← "on service since 1909hrs" = 19:09 (split 1909 as 19:09)
  "duration_minutes": 430,            ← 7*60 + 10 = 430 minutes
  "status": "Closed"                  ← "cells are on service" = Closed (plural form)
}

CRITICAL INTELLIGENCE RULES:

1. **Natural English Understanding**:
   - "yesterday at 8pm" → Calculate actual date/time from TODAY'S DATE
   - "last night" → Yesterday evening
   - "this morning" → Today morning
   - "3 hours ago" → Calculate from CURRENT TIME
   - "went down at 7pm, came back at 4am" → Calculate duration = 9 hours = 540 minutes

2. **Service Identification**:
   - Extract ONLY service identifier: "L_Hithadhoo FBB", "Kandima Resort 3G/4G"
   - Recognize variations: "broadband", "FBB", "fixed line", "fiber" → FBB
   - "cell sites", "tower", "base station", "AAU", "RRU" → RAN/Cell
   - Handle informal naming: "the Hithadhoo fiber" → "L_Hithadhoo FBB"

3. **Status Detection**:
   CRITICAL: Check the PRIMARY action verb to determine status

   Status = Closed (service RESTORED) if message contains:
   - "on service" (both singular and plural: "cell is on service" OR "cells are on service")
   - "restored", "back online", "fixed", "resolved", "working again", "came back"

   Status = Open (service DOWN) if message contains:
   - "down", "offline", "not working", "outage", "issue", "problem", "failure"

   CRITICAL EXAMPLES:
   - "Below mentioned cell is on service since..." → Status = Closed ✓
   - "Below mentioned cells are on service since..." → Status = Closed ✓
   - "Below mentioned cells are down since..." → Status = Open ✓

   CRITICAL: "on service since [TIME]" means RESTORED at that time, NOT started!
   - If you see "on service since [TIME]" → status = Closed, resolved_at = TIME
   - Then SUBTRACT duration to get started_at
   - Example: "on service since 1220hrs, Duration: 30mins" → resolved: 12:20, started: 11:50

4. **Duration Calculation & Time Logic**:
   - If start and end times given but no duration → Calculate it
   - If duration given (e.g., "took 3 hours") → Convert to minutes
   - "all day" ≈ 8-12 hours, "overnight" ≈ 8-10 hours

   CRITICAL: "Total Down Duration" Detection:
   - If message contains "total down duration", "TDD", or mentions duration will be calculated/updated manually
   - Set duration_minutes to null (don't auto-calculate)
   - Set started_at and resolved_at to null if they can't be determined
   - This signals manual entry is required

   CRITICAL CALCULATION LOGIC (DO NOT GET THIS WRONG):
   - "on service since [TIME], Duration: [X]" → resolved_at = TIME, started_at = TIME - X (SUBTRACT!)
     Example: "on service since 16:22, Duration: 8hrs 37mins" → resolved=16:22, started=07:45
   - "down since [TIME], Duration: [X]" → started_at = TIME, resolved_at = TIME + X (ADD!)
     Example: "down since 08:00, Duration: 2hrs" → started=08:00, resolved=10:00
   - "went down at [TIME1], restored at [TIME2]" → started_at = TIME1, resolved_at = TIME2
   - Always verify: duration = resolved_at - started_at (must equal given duration)

5. **Cause Classification**:
   - Power keywords: power failure, power outage, DCDU, breaker, battery, generator
   - Transmission keywords: cable cut, fiber damaged, cable damaged, link down
   - RAN keywords: equipment failure, AAU issue, RRU problem, site down
   - Weather-related → Note the actual technical cause, weather goes in delay_reason

6. **Delay Reason**:
   - Extract if duration > 5 hours (300 minutes) AND reason is mentioned
   - Look for: "delayed because", "took longer due to", "Note:", weather mentions, access issues

7. **Affected Services**:
   - Single FBB → ["Single FBB"]
   - Multiple FBB → ["Multiple FBB"]
   - Single site/tower → ["Single Site"]
   - CRITICAL: For multiple cells → ["Cell"] (this is the default)
   - ONLY use ["Multiple Site"] if the message text LITERALLY contains the word "sites" (plural)
   - NEVER use "Multiple Site" just because there are multiple cells - you MUST see "sites" in the text
   - Examples that should be ["Cell"]:
     * "Below mentioned cells are down..." → ["Cell"] (says "cells", not "sites")
     * "K_Cell1, K_Cell2, K_Cell3 are down" → ["Cell"] (no "sites" word found)
     * "The following cells went offline" → ["Cell"] (says "cells", not "sites")
   - Examples that should be ["Multiple Site"]:
     * "Multiple sites in Kandima are down" → ["Multiple Site"] (contains "sites")
     * "3 sites affected" → ["Multiple Site"] (contains "sites")
     * "Several sites are offline" → ["Multiple Site"] (contains "sites")

8. **Date/Time Parsing**:
   - Convert ALL relative dates to absolute YYYY-MM-DD HH:MM format
   - Use TODAY'S DATE and CURRENT TIME for calculations
   - Default unknown times: morning=08:00, afternoon=14:00, evening=18:00, night=22:00

   CRITICAL: 4-digit time format parsing:
   - "1622hrs" = 16:22 (split as HH:MM → 16:22)
   - "0945hrs" = 09:45 (split as HH:MM → 09:45)
   - "2119hrs" = 21:19 (split as HH:MM → 21:19)
   - ALWAYS split 4-digit numbers as first 2 digits = hours, last 2 digits = minutes

Return ONLY valid JSON with NO markdown formatting, NO code blocks, NO explanatory text.
PROMPT;
    }

    /**
     * Test the API connection.
     *
     * @return array Test result with status and message
     */
    public function testConnection(): array
    {
        try {
            $response = $this->client->messages->create([
                'model' => $this->model,
                'max_tokens' => 50,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Reply with just the word "Connected" if you can read this.',
                    ],
                ],
            ]);

            return [
                'status' => 'success',
                'message' => 'API connection successful',
                'response' => $response->content[0]->text,
                'model' => $this->model,
                'tokens_used' => $response->usage->input_tokens + $response->usage->output_tokens,
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'API connection failed: ' . $e->getMessage(),
            ];
        }
    }
}
