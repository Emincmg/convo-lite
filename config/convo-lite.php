<?php

return [

    /*
    |---------------------------------------------------------------------------
    | User Model
    |---------------------------------------------------------------------------
    |
    | This value is the path to your user model for your conversations. You can change this to your own model if you
    | like. Keep in mind that it has to be an Authenticatable type of model in order to use the package without any
    | issues.
    */

    'user_model' => config('auth.providers.users.model','App\\Models\\User.php'),


    /*
    |---------------------------------------------------------------------------
    | Send Message Notifications
    |---------------------------------------------------------------------------
    |
    | This controls whether send email to the receivers upon sent messages. Its disabled by default, change this if you
    | want to enable sending notifications via mail upon receiving messages.
    */

    'send_message_notifications' => false,
];
