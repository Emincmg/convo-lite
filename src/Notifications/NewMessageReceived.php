<?php

namespace App\Notifications;

use Emincmg\ConvoLite\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(public Message $message)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New message notification from' . ' ' . $this->message->senderName)
            ->greeting("Dear " . $notifiable->first_name . " " . $notifiable->last_name . ',')
            ->line("You've got a new message from " . $this->message->senderName . " in Blue Palm Website.")
            ->line("Click the button below to view.")
            ->action('Details', config('convo-lite.app_url'))
            ->line('Best Regards,')
            ->salutation(env('APP_NAME'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
//
        ];
    }
}
