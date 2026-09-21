<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Queue delivery
    |--------------------------------------------------------------------------
    | When true, notifications are pushed onto the queue and delivered by a
    | worker (`php artisan queue:work`). When false, they are sent immediately
    | during the request/event that triggered them.
    */
    'queue' => env('PORTAL_NOTIFICATIONS_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Mail delivery
    |--------------------------------------------------------------------------
    | When true, every notification is also delivered via e-mail. Requires
    | working MAIL_* settings in .env and a valid e-mail on the notifiable
    | user record.
    */
    'mail' => env('PORTAL_NOTIFICATIONS_MAIL', false),

    /*
    |--------------------------------------------------------------------------
    | Notify in console
    |--------------------------------------------------------------------------
    | Model events fired from artisan commands, seeders, or queue workers are
    | skipped unless this is true. This prevents a seeder or bulk import from
    | accidentally sending thousands of notifications.
    */
    'notify_in_console' => false,

    /*
    |--------------------------------------------------------------------------
    | Fee due reminder window
    |--------------------------------------------------------------------------
    | Number of days before an installment's due date when the student is
    | notified. Used by the SendNotificationReminders command.
    */
    'fee_due_days' => 3,

    /*
    |--------------------------------------------------------------------------
    | Automatic triggers
    |--------------------------------------------------------------------------
    | Toggle each automatic notification trigger on/off. Disabling a trigger
    | here stops its corresponding listener/observer from firing portal
    | notifications, without removing any code.
    */
    'triggers' => [
        'attendance_absent'    => true,
        'exam'                 => true,
        'leave'                => true,
        'salary'               => true,
        'fee_installment'      => true,
        'invoice'              => true,
        'subject_assignment'   => true,
        'admission'            => true,
        'certificate'          => true,
        'transfer_certificate' => true,
        'hostel'               => true,
        'transport'            => true,
        'student'              => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification categories
    |--------------------------------------------------------------------------
    | Optional list of allowed categories. Used for validation and for
    | grouping in the UI. Add or remove freely.
    */
    'categories' => [
        'general',
        'attendance',
        'exam',
        'leave',
        'salary',
        'fee',
        'invoice',
        'subject',
        'admission',
        'certificate',
        'transfer_certificate',
        'hostel',
        'transport',
        'student',
    ],

];