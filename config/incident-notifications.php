<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Incident Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure email/SMS notification recipients for incident alerts.
    | Notifications are sent based on severity levels.
    |
    */

    'enabled' => env('INCIDENT_NOTIFICATIONS_ENABLED', true),

    // Email configuration
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'incidents@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Incident Management System'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Levels
    |--------------------------------------------------------------------------
    |
    | Define which email addresses receive notifications based on severity.
    | Level 1: Critical/High severity incidents
    | Level 2: Medium severity incidents
    | Level 3: Low severity incidents or all incidents
    |
    */

    'levels' => [
        'level_1' => [
            'name' => 'Level 1 - Critical Alerts',
            'description' => 'High priority incidents only',
            'recipients' => env('INCIDENT_LEVEL_1_RECIPIENTS', ''),
            'severity' => ['High'],
        ],

        'level_2' => [
            'name' => 'Level 2 - Standard Alerts',
            'description' => 'Medium and High priority incidents',
            'recipients' => env('INCIDENT_LEVEL_2_RECIPIENTS', ''),
            'severity' => ['Medium', 'High'],
        ],

        'level_3' => [
            'name' => 'Level 3 - All Alerts',
            'description' => 'All incidents regardless of severity',
            'recipients' => env('INCIDENT_LEVEL_3_RECIPIENTS', ''),
            'severity' => ['Low', 'Medium', 'High'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Events
    |--------------------------------------------------------------------------
    |
    | Configure which events trigger notifications.
    |
    */

    'events' => [
        'created' => true,       // Send notification when incident is created
        'updated' => true,       // Send notification when incident is updated (timeline entry added)
        'closed' => true,        // Send notification when incident is closed
        'sla_breached' => true,  // Send notification when incident breaches SLA
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Settings (Future)
    |--------------------------------------------------------------------------
    |
    | SMS notification settings for when API is integrated
    |
    */

    'sms' => [
        'enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
        'api_url' => env('SMS_API_URL', ''),
        'api_key' => env('SMS_API_KEY', ''),
    ],

];
