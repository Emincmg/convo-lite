# Convo Lite

Convo Lite is a package that allows you to easily create and manage chat rooms in your Laravel application. This package
enables your users to communicate with each other.

# Installation

### Include the package

You can include Convo Lite in your project using composer:

```bash
composer require emincmg/convo-lite
```

### Migrate

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

Publish the package's publishable files by running the following command:

```bash
php artisan vendor:publish --provider="Emincmg\ConvoLite\Providers\ConversationServiceProvider"
```

This will publish the migration fies to your applications `database/migrations` folder, and config file to your applications `config` folder.
# Configuration


### Changing Default Model

You can change the default model for creating conversations between them through `config/convo_lite.php`

config file should be already published upon triggering ```vendor:publish``` command

```php
'user_model' => config('auth.providers.users.model','App\\Models\\User.php'),
```

This defaults to your applications default model, so you can change that or if you only change for this package change
this field.

### Localization

All translation files are published to `lang/vendor/convo_lite` folder and the language's abbreviation (e.g. /en for English).

```php
return [
    'new_message_subject' => 'New message notification from :sender',
    'greeting' => 'Dear :name,',
    'new_message_line' => 'You\'ve got a new message from :sender.',
    'click_to_view' => 'Click the button below to view.',
    'view_details' => 'Details',
    'best_regards' => 'Best Regards,',
    'slack_message' => 'You\'ve got a new message from :sender.',
    'view_message' => 'View Message',
    'sms_message' => 'You\'ve got a new message from :sender.',
];
```

# Usage

To start using Convo Lite, you can add some basic routes and controller methods to manage conversations and messages.
Here is an example usage:

### Creating a conversation

Receiver could be both integer or array if multiple. If multiple receiver provided, conversation will be created between
them.

```php
$senderId= 1;
$receiverIds = [2,3];

Convo::createConversation($senderId,$receiverIds);
```

This will return the conversation that has just been created;

```json
[
    {
        "title": null,
        "updated_at": "2024-06-06T14:18:50.000000Z",
        "created_at": "2024-06-06T14:18:50.000000Z",
        "id": 1
    }
]
```

To set a title for conversation, you could either;

```php
$conversation = Convo::getConversationById(1);
$conversation->setTitle('Test Title');
```

or just create the conversation with title included.

```php
 Convo::createConversation($senderId,$receiverIds,'Test Title');
```

both will return the same thing;

```json
[
    {
        "title": "Test Title",
        "updated_at": "2024-06-06T14:18:50.000000Z",
        "created_at": "2024-06-06T14:18:50.000000Z",
        "id": 1
    }
]
```

if multiple receivers are provided, response will be a collection of conversations that has been created.

### Get a conversation

```php
$conversation = Convo::getConversationById(1);
```

### Add Participators

You can attach participators to an existing conversation by;

````php
$conversation = Convo::getConversationById(1);
$userId = 1;

Convo::addParticipators($conversation, $userIds)
````
or you can send only the ID of the conversation;
````php
$conversationId = 1;
$userId = 1;

Convo::addParticipators($conversationId, $userId)
````

you can add multiple participators as well;

````php
$conversationId = 1;
$userIds = [1,2,3,4];

Convo::addParticipators($conversationId, $userIds)
````

### Send Message

Send a message by facade (files are optional);

```php
$conversationId = 1;
$message = Convo::sendMessage($conversationId,$user,'hello',$request->files());
```
or you can pass conversation instance directly.
````php
$conversation = Convo::getConversationById(1);
$message = Convo::sendMessage($conversation,$user,'hello',$request->files());
````



### Get messages

Get by conversation model

```php
$conversation = Convo::getConversationById(1);
$messages = Convo::getMessagesByConversation($conversation);
```

or you can access its messages directly by conversation instance;

```php
$conversation = Convo::getConversationById(1);
$conversation->messages;
```

# Broadcasting

Convo Lite supports Broadcasting via events and listeners.

### Broadcasting Authorization

Events are broadcasted to the private channels of the users : `user + id (eg user.1)` . I recommend using Laravel Reverb on the backend along with Echo on the frontend for fast & secure implementation. Check out their [Documentation here](https://laravel.com/docs/11.x/reverb)

### Queues

Events are pushed to the `queues` specified in the `config/convo_lite.php` file

```php
'queues' => [
'mail' => 'convo-lite.mail',
'broadcast' => 'convo-lite.broadcast',
'slack' => 'convo-lite.slack',
'nexmo' => 'convo-lite.nexmo',
],
```

In order to process the events that got triggered, you should start your workers like this;

```bash
php artisan queue:work --queue=convo-lite.mail,convo-lite.broadcast,default
```

For broadcasting to work, `default` queue worker should be always started.

Convo Lite is open-source software licensed under the [MIT license](LICENSE.md).

