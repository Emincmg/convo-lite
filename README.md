
# Convo Lite

Convo Lite is a package that allows you to easily create and manage chat rooms in your Laravel application. This package enables your users to communicate with each other.

## Installation

You can include Convo Lite in your project using composer:

```bash
composer require emincmg/convo-lite
```

## Configuration

After the package is installed, add the service provider to the `providers` array in your `config/app.php` file:

```php
'providers' => [
    // Other service providers

    ConvoLite\ConversationServiceProvider::class,
],
```

Publish the package's publishable files by running the following command:

```bash
php artisan vendor:publish --provider="ConvoLite\ConversationServiceProvider"
```

## Database Migrations

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

## Usage

To start using Convo Lite, you can add some basic routes and controller methods to manage chat rooms and messages. Here is an example usage:

### Creating a conversation

Receiver could be both integer or array if multiple. If multiple receiver provided, more than one conversation will be created between sender and receivers.

```php
$senderId= 1;
$receiverIds = [2,3];

Convo::createConversation($senderId,$receiverIds);
```
This will return the conversation that has just been created; 

```json
{"title":null,"updated_at":"2024-06-03T06:56:20.000000Z","created_at":"2024-06-03T06:56:20.000000Z","id":15}
```
To set a title for conversation, you could either;

```php
$conversation = Convo::createConversation($senderId,$receiverIds);
$conversation->setTitle('Test Title');
```
or just create the conversation with title included.

```php
 Convo::createConversation({senderId}, {receiverId(s),'Test Title');
```
 
both will return the same thing;

```json
{"title":"Test Title","updated_at":"2024-06-03T06:56:20.000000Z","created_at":"2024-06-03T06:56:20.000000Z","id":15}
```

### Get a conversation

Get by id
```php
$conversation = Convo::getConversationById(1);
```
Get by title
```php
$conversation = Convo::getConversationByTitle()
```
## License

Convo Lite is open-source software licensed under the [MIT license](LICENSE.md).

