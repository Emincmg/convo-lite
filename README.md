
# Convo Lite

Convo Lite is a package that allows you to easily create and manage chat rooms in your Laravel application. This package enables your users to communicate with each other.

## Installation

You can include Convo Lite in your project using composer:

```bash
composer require emincmg/convo-lite
```

## Configuration

Publish the package's publishable files by running the following command:

```bash
php artisan vendor:publish --provider="Emincmg\ConvoLite\Providers\ConversationServiceProvider"
```
This will publish the migrations and 
### Changing Default Model

You can change the default model for creating conversations between them through `config/convo_lite.php`
```php
'user_model' => config('auth.providers.users.model','App\\Models\\User.php'),
```
This defaults to your applications default model, so you can change that or if you only change for this package change this field.
## Database Migrations

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

## Usage

To start using Convo Lite, you can add some basic routes and controller methods to manage conversations and messages. Here is an example usage:

### Creating a conversation

Receiver could be both integer or array if multiple. If multiple receiver provided, more than one conversation will be created between sender and receivers.

```php
$senderId= 1;
$receiverIds = [2,3];

Convo::createConversation($senderId,$receiverIds);
```
This will return the conversation that has just been created; 

```json
{
    "title":null,
    "updated_at":"2024-06-03T06:56:20.000000Z",
    "created_at":"2024-06-03T06:56:20.000000Z",
    "id":15
}
```
To set a title for conversation, you could either;

```php
$conversation = Convo::createConversation($senderId,$receiverIds);
$conversation->setTitle('Test Title');
```
or just create the conversation with title included.

```php
 Convo::createConversation($senderId,$receiverIds,'Test Title');
```
 
both will return the same thing;

```json
{
    "title":"Test Title",
    "updated_at":"2024-06-03T06:56:20.000000Z",
    "created_at":"2024-06-03T06:56:20.000000Z",
    "id":15
}
```

### Get a conversation

Get by id
```php
$conversation = Convo::getConversationById(1);
```
Get by title
```php
$conversation = Convo::getConversationByTitle('Title')
```

### Sending message

```php
$conversation = Convo::getConversationById(1);
$message = Convo::sendMessage($conversation,$message,$sender)
```
### Get messages
Get by conversation model
```php
$conversation = Convo::getConversationById(1);
$messages = Convo::getMessagesByConversation($conversation);
```
or,
```php
$conversation = Convo::getConversationById(1);
$conversation->messages;
```
## License

Convo Lite is open-source software licensed under the [MIT license](LICENSE.md).

