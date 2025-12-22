<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class CloseIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $data = $this->all();
        
        // Filter logs - remove empty entries and template entries with "INDEX"
        if (isset($data['logs']) && is_array($data['logs'])) {
            $data['logs'] = array_filter($data['logs'], function ($log) {
                return !empty($log['occurred_at']) && !empty($log['note']) 
                    && !str_contains($log['occurred_at'], 'INDEX') 
                    && !str_contains($log['note'], 'INDEX');
            });
            $data['logs'] = array_values($data['logs']); // Re-index array
        }
        
        // Filter action points - remove empty entries and template entries with "INDEX"
        if (isset($data['action_points']) && is_array($data['action_points'])) {
            $data['action_points'] = array_filter($data['action_points'], function ($actionPoint) {
                return !empty($actionPoint['description']) && !empty($actionPoint['due_date'])
                    && !str_contains($actionPoint['description'], 'INDEX')
                    && !str_contains($actionPoint['due_date'], 'INDEX');
            });
            $data['action_points'] = array_values($data['action_points']); // Re-index array
        }
        
        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        // Only apply these rules when closing an incident
        if ($this->input('status') === 'Closed') {
            $startedAt = $this->input('started_at');
            $resolvedAt = $this->input('resolved_at');
            $severity = $this->input('severity');

            // Rule 1: If duration > 5 hours, delay_reason required
            if ($startedAt && $resolvedAt) {
                $start = Carbon::parse($startedAt);
                $end = Carbon::parse($resolvedAt);
                $durationHours = $start->diffInHours($end);

                if ($durationHours > 5) {
                    $rules['delay_reason'] = 'required|string';
                }
            }

            // Rule 2: travel_time and work_time are optional but must be valid if provided
            $rules['travel_time'] = 'nullable|integer|min:0';
            $rules['work_time'] = 'nullable|integer|min:0';

            // Get the incident to check for RCA Management entry
            $incident = $this->route('incident');
            $hasRcaManagementEntry = $incident && $incident->rca;

            // Rule 3: High severity incident requirements
            if ($severity === 'High') {
                $rules['corrective_actions'] = 'required|string';
                $rules['workaround'] = 'required|string';
                $rules['solution'] = 'required|string';
                $rules['recommendation'] = 'required|string';

                // Check if RCA exists - either from RCA Management or uploaded file
                if ($incident && !$hasRcaManagementEntry && !$incident->hasRcaFile() && !$this->hasFile('rca_file')) {
                    $rules['rca_file'] = 'required|file|mimes:pdf,doc,docx|max:10240';
                }
            }

            // Rule 3.5: Critical severity incident RCA requirement
            if ($severity === 'Critical') {
                // Check if RCA exists - either from RCA Management or uploaded file
                if ($incident && !$hasRcaManagementEntry && !$incident->hasRcaFile() && !$this->hasFile('rca_file')) {
                    $rules['rca_file'] = 'required|file|mimes:pdf,doc,docx|max:10240';
                }
            }

            // Rule 4: Critical severity incident requirements (only if no RCA Management entry)
            if ($severity === 'Critical' && !$hasRcaManagementEntry) {
                // At least one log entry required
                $rules['logs'] = 'required|array|min:1';
                $rules['logs.*.occurred_at'] = 'required|date';
                $rules['logs.*.note'] = 'required|string|max:1000';

                // At least one action point required
                $rules['action_points'] = 'required|array|min:1';
                $rules['action_points.*.description'] = 'required|string';
                $rules['action_points.*.due_date'] = 'required|date';
            }
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'delay_reason.required' => 'Reason for delay is required when incident duration exceeds 5 hours.',
            'travel_time.integer' => 'Travel time must be a valid number of minutes.',
            'travel_time.min' => 'Travel time cannot be negative.',
            'work_time.integer' => 'Work time must be a valid number of minutes.',
            'work_time.min' => 'Work time cannot be negative.',
            
            // High severity requirements
            'corrective_actions.required' => 'Corrective Actions are required for High severity incidents.',
            'workaround.required' => 'Workaround is required for High severity incidents.',
            'solution.required' => 'Solution is required for High severity incidents.',
            'recommendation.required' => 'Recommendation is required for High severity incidents.',
            
            // Critical severity requirements
            'logs.required' => 'At least one log entry is required for Critical severity incidents.',
            'logs.min' => 'At least one log entry is required for Critical severity incidents.',
            'action_points.required' => 'At least one action point is required for Critical severity incidents.',
            'action_points.min' => 'At least one action point is required for Critical severity incidents.',
            'action_points.*.description.required' => 'Action point description is required.',
            'action_points.*.due_date.required' => 'Action point due date is required.',
            
            // RCA file requirements
            'rca_file.required' => 'RCA is required for High and Critical severity incidents. Please either create an RCA through RCA Management or upload an RCA file (PDF or Word document).',
            'rca_file.file' => 'RCA must be a valid file.',
            'rca_file.mimes' => 'RCA file must be a PDF or Word document (PDF, DOC, or DOCX format).',
            'rca_file.max' => 'RCA file size must not exceed 10MB.',
        ];
    }

    /**
     * Custom validation after basic validation passes
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Only validate when closing Critical incidents
            if ($this->input('status') === 'Closed' && $this->input('severity') === 'Critical') {
                // Check if incident exists (for updates)
                $incident = $this->route('incident');
                if ($incident) {
                    // Check if RCA Management entry exists
                    $hasRcaManagementEntry = $incident->rca;

                    // Only check action points completion if no RCA Management entry exists
                    if (!$hasRcaManagementEntry) {
                        // For existing incidents, check if all action points are completed
                        if (!$incident->hasAllActionPointsCompleted()) {
                            $validator->errors()->add('action_points', 'All action points must be completed before closing a Critical incident.');
                        }
                    }
                }
            }
        });
    }

    /**
     * Manually trigger validation for this request
     */
    public function validateResolved()
    {
        $validator = $this->getValidatorInstance();

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        return $validator->validated();
    }
}
