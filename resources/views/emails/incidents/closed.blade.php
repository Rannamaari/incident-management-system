<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incident Resolved</title>
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
            background-color: #10b981;
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
        .message {
            white-space: pre-line;
            background-color: white;
            padding: 15px;
            border-left: 4px solid #10b981;
            margin: 10px 0;
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
        <h2 style="margin: 0;">Incident Resolved: Service Restored</h2>
    </div>
    <div class="content">
        <p><strong>This incident has been resolved:</strong></p>
        <div class="message">@include('emails.incidents.closed-text')</div>

        <p style="margin-top: 20px;"><strong>Incident Code:</strong> {{ $incident->incident_code }}</p>
        @if($incident->duration_minutes)
        <p><strong>Total Downtime:</strong> {{ $incident->duration_hms }}</p>
        @endif
        <p><strong>Status:</strong> <span style="color: #10b981">{{ $incident->status }}</span></p>

        <div class="footer">
            <p>This is an automated notification from the Incident Management System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
