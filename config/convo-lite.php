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

    'user_model' => config('auth.providers.users.model', 'App\\Models\\User'),


    /*
    |--------------------------------------------------------------------------
    | Message Notification Channels
    |--------------------------------------------------------------------------
    |
    | This option controls whether to send notifications to message recipients. By default, email notifications are disabled.
    | To enable notifications, specify the channels (e.g., "mail, broadcast(for WebSocket)") in this setting to notify users
    | when they receive messages.
    |
    */

    'notification_channels' => env('CONVO_LITE_NOTIFICATION_CHANNELS', ['broadcast']),

    /*
   |--------------------------------------------------------------------------
   | Custom Queues
   |--------------------------------------------------------------------------
   |
   | This option allows you to define custom queue names for different types
   | of notifications. By specifying these queues, you can better organize
   | and manage your notification delivery processes. Each type of notification,
   | such as mail, broadcast, Slack, or Nexmo, can be assigned to a specific queue.
   |
   | Example:
   | - 'mail' => 'convo-lite.mail' (Queue for mail notifications)
   | - 'broadcast' => 'convo-lite.broadcast' (Queue for broadcast notifications)
   | - 'slack' => 'convo-lite.slack' (Queue for Slack notifications)
   | - 'nexmo' => 'convo-lite.nexmo' (Queue for Nexmo/SMS notifications)
   |
   */

    'queues' => [
        'mail' => 'convo-lite.mail',
        'broadcast' => 'convo-lite.broadcast',
        'slack' => 'convo-lite.slack',
        'nexmo' => 'convo-lite.nexmo',
    ],


    /*
    |--------------------------------------------------------------------------
    | WebSocket Support
    |--------------------------------------------------------------------------
    |
    | This option enables or disables WebSocket support for the package. By default, WebSocket functionality is turned off.
    | If you enable this feature, a pre-configured WebSocket server will be activated to facilitate real-time communication
    | within your application.
    |
    */

    'websocket_enabled' => env('CONVO_LITE_WEBSOCKET_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | WebSocket Port
    |--------------------------------------------------------------------------
    |
    | Define the port that websocket will be worked on. 8080 by default
    |
    */
    'websocket_port' => env('CONVERSATION_WEBSOCKET_PORT', 8080),
];
