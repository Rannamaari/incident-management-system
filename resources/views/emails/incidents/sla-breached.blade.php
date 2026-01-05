<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SLA Breach Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #ea580c;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .warning-box {
            background-color: #fff7ed;
            border-left: 4px solid #ea580c;
            padding: 15px;
            margin: 15px 0;
        }
        .message {
            white-space: pre-line;
            background-color: white;
            padding: 15px;
            border-left: 4px solid #dc2626;
            margin: 10px 0;
        }
        .sla-info {
            background-color: #fef2f2;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">SLA BREACH ALERT</h2>
    </div>
    <div class="content">
        <div class="warning-box">
            <p style="margin: 0; font-weight: bold; color: #ea580c;">
                This incident has exceeded its SLA threshold and requires immediate attention!
            </p>
        </div>

        <p><strong>Incident Details:</strong></p>
        <div class="message">@include('emails.incidents.sla-breached-text')</div>

        <div class="sla-info">
            <p style="margin: 5px 0;"><strong>Incident Code:</strong> {{ $incident->incident_code }}</p>
            <p style="margin: 5px 0;"><strong>Severity:</strong> <span style="color: {{ $incident->severity === 'High' ? '#dc2626' : ($incident->severity === 'Medium' ? '#f59e0b' : '#10b981') }}">{{ $incident->severity }}</span></p>
            <p style="margin: 5px 0;"><strong>Status:</strong> {{ $incident->status }}</p>
            <p style="margin: 5px 0;"><strong>SLA Target:</strong> {{ $incident->sla_minutes }} minutes ({{ number_format($incident->sla_minutes / 60, 1) }} hours)</p>
            @if($incident->status === 'Closed')
                <p style="margin: 5px 0;"><strong>Actual Duration:</strong> {{ $incident->duration_minutes }} minutes ({{ number_format($incident->duration_minutes / 60, 1) }} hours)</p>
                <p style="margin: 5px 0; color: #dc2626;"><strong>Exceeded By:</strong> {{ $incident->duration_minutes - $incident->sla_minutes }} minutes</p>
            @else
                <p style="margin: 5px 0;"><strong>Time Elapsed:</strong> {{ $incident->started_at->diffInMinutes(now()) }} minutes ({{ number_format($incident->started_at->diffInMinutes(now()) / 60, 1) }} hours)</p>
                <p style="margin: 5px 0; color: #dc2626;"><strong>Exceeded By:</strong> {{ $incident->started_at->diffInMinutes(now()) - $incident->sla_minutes }} minutes</p>
            @endif
            <p style="margin: 5px 0;"><strong>Started At:</strong> {{ $incident->started_at->format('M d, Y H:i') }}</p>
        </div>

        <p style="background-color: #fff7ed; padding: 10px; border-radius: 5px; margin-top: 15px;">
            <strong>Action Required:</strong> This incident requires immediate escalation and resolution to minimize service impact.
        </p>

        <div class="footer">
            <p>This is an automated notification from the Incident Management System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
