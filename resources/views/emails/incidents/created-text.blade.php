@php
    // Format time as HHMMhrs DD/MM/YYYY
    $startedTime = $incident->started_at->format('Hi') . 'hrs ' . $incident->started_at->format('d/m/Y');

    // Determine if it's a site outage or cell outage
    $isSiteOutage = in_array('Single Site', explode(', ', $incident->affected_services ?? '')) ||
                    in_array('Multiple Site', explode(', ', $incident->affected_services ?? ''));
    $isCellOutage = in_array('Cell', explode(', ', $incident->affected_services ?? ''));
    $isRHUBOutage = str_contains($incident->affected_services ?? '', 'RHUB');

    // Get technology info from summary if available
    $summary = $incident->summary;
@endphp
@if($isRHUBOutage)
Below mentioned RHUB is down since {{ $startedTime }}
{{ $summary }}
@elseif($isCellOutage && !$isSiteOutage)
Below mentioned cells are down since {{ $startedTime }}
{{ $summary }}
@else
{{ $summary }} is down since {{ $startedTime }}
@endif
Cause: {{ $incident->root_cause ?: 'Under investigation' }}
