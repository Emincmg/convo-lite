Tabii ki, işte İngilizce olarak hazırlanmış bir README.md dosyası:

```markdown
# Convo Lite

Convo Lite is a package that allows you to easily create and manage chat rooms in your Laravel application. This package enables your users to communicate with each other in real-time.

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

### Creating a Chat Room

Create a conversation:

```php
 Conversation::createConversation({senderId}, {receiverId(s)});
```

## Contributing

Thank you for considering contributing to Convo Lite! You can read the contribution guide [here](CONTRIBUTING.md).

## License

Convo Lite is open-source software licensed under the [MIT license](LICENSE.md).
```

This README provides basic installation, configuration, and usage instructions for the Convo Lite package. You can copy this and save it as `README.md` in your project.
