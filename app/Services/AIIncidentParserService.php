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
        return <<<PROMPT
Extract ALL relevant fields from this incident message and return them as JSON.

Message:
{$message}

Extract these fields (return null for any field that cannot be determined):
{
  "summary": "Service/site name with technology (e.g., 'L_Hithadhoo FBB', 'Dh_Kandima_Resort 3G/4G')",
  "root_cause": "The cause description",
  "delay_reason": "Extract from Note field if present and if duration > 5 hours",
  "affected_services": ["Array of affected services like 'Single FBB', 'Cell', etc."],
  "outage_category": "One of: Power, RAN, Transmission, International, Enterprise, FBB",
  "category": "Same as outage_category usually"
}

Important Rules:
1. summary: ONLY the service identifier, no dates, no "is on service", no descriptive text
2. delay_reason: If you see a "Note:" field AND the duration is > 5 hours, extract the Note content as delay_reason
3. affected_services: Determine from the service type (FBB → ["Single FBB"], cells → ["Cell"], etc.)
4. outage_category & category:
   - Power = power failure, DCDU tripped, breaker issues
   - RAN = cell tower, AAU, RRU, site issues
   - Transmission = fiber cut, cable damage
   - FBB = fixed broadband
   - If unclear, return null

Return ONLY valid JSON, no explanation text.
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
