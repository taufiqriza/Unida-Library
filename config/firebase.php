<?php

return [
    /*
     * Firebase Project ID
     */
    'project_id' => env('FIREBASE_PROJECT_ID', 'unida-library'),

    /*
     * Path to service account credentials JSON file
     * Store in storage/app/firebase/
     */
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS') 
            ? storage_path('app/firebase/' . env('FIREBASE_CREDENTIALS'))
            : null,
    ],

    /*
     * Legacy FCM Server Key (alternative if not using Admin SDK)
     */
    'fcm_server_key' => env('FCM_SERVER_KEY'),

    /*
     * Default notification settings
     */
    'notification' => [
        'sound' => 'default',
        'badge' => 1,
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
    ],
];
