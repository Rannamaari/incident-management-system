<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incident Alert</title>
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
            background-color: #dc2626;
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
            border-left: 4px solid #dc2626;
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
        <h2 style="margin: 0;">Incident Alert: Service Down</h2>
    </div>
    <div class="content">
        <p><strong>An incident has been reported:</strong></p>
        <div class="message">@include('emails.incidents.created-text')</div>

        <p style="margin-top: 20px;"><strong>Incident Code:</strong> {{ $incident->incident_code }}</p>
        <p><strong>Severity:</strong> <span style="color: {{ $incident->severity === 'High' ? '#dc2626' : ($incident->severity === 'Medium' ? '#f59e0b' : '#10b981') }}">{{ $incident->severity }}</span></p>
        <p><strong>Status:</strong> {{ $incident->status }}</p>

        <div class="footer">
            <p>This is an automated notification from the Incident Management System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
