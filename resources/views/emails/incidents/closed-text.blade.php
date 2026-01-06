@php
    // Format time as HHMMhrs DD/MM/YYYY
    $resolvedTime = $incident->resolved_at ? $incident->resolved_at->format('Hi') . 'hrs ' . $incident->resolved_at->format('d/m/Y') : 'N/A';

    // Format duration
    $durationText = '';
    if ($incident->duration_minutes) {
        $hours = floor($incident->duration_minutes / 60);
        $minutes = $incident->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            $durationText = $hours . 'hrs ' . $minutes . 'mins';
        } elseif ($hours > 0) {
            $durationText = $hours . 'hrs';
        } else {
            $durationText = $minutes . 'mins';
        }
    }

    // Determine if it's a site outage or cell outage
    $isSiteOutage = in_array('Single Site', explode(', ', $incident->affected_services ?? '')) ||
                    in_array('Multiple Site', explode(', ', $incident->affected_services ?? ''));
    $isCellOutage = in_array('Cell', explode(', ', $incident->affected_services ?? ''));
    $isRHUBOutage = str_contains($incident->affected_services ?? '', 'RHUB');

    // Get technology info from summary if available
    $summary = $incident->summary;
@endphp
@if($isRHUBOutage)
Below mentioned RHUB is on service since {{ $resolvedTime }}
{{ $summary }}
@elseif($isCellOutage && !$isSiteOutage)
Below mentioned cells are on service since {{ $resolvedTime }}
{{ $summary }}
@else
{{ $summary }} is on service since {{ $resolvedTime }}
@endif
@if($durationText)
Duration: {{ $durationText }}
@endif
Cause: {{ $incident->root_cause ?: 'Under investigation' }}
