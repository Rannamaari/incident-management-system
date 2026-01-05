<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incident Update</title>
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
            background-color: #f59e0b;
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
            border-left: 4px solid #f59e0b;
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
        <h2 style="margin: 0;">Incident Update</h2>
    </div>
    <div class="content">
        <p><strong>An update has been posted for this incident:</strong></p>
        <div class="message">{{ $updateMessage }}</div>

        <p style="margin-top: 15px; padding: 10px; background-color: #f0f9ff; border-left: 3px solid #3b82f6; border-radius: 3px;">
            <strong>Posted by:</strong> {{ $userName }}<br>
            <strong>Time:</strong> {{ now()->format('M d, Y H:i') }}
        </p>

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
