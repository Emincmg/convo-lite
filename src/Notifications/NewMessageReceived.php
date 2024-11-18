<?php

namespace Emincmg\ConvoLite\Notifications;

use Emincmg\ConvoLite\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Message $message)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return config('convo-lite.notification_channels');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.new_message_subject', ['sender' => $this->message->senderName]))
            ->greeting(__('notifications.greeting', ['name' => $notifiable->first_name . ' ' . $notifiable->last_name]))
            ->line(__('notifications.new_message_line', ['sender' => $this->message->senderName]))
            ->line(__('notifications.click_to_view'))
            ->action(__('notifications.view_details'), config('convo-lite.app_url'))
            ->line(__('notifications.best_regards'))
            ->salutation(env('APP_NAME'));
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message_id' => $this->message->id,
            'sender_name' => $this->message->senderName,
            'content' => $this->message->body,
        ]);
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->success()
            ->content(__('notifications.slack_message', ['sender' => $this->message->senderName]))
            ->attachment(function ($attachment) {
                $attachment->title(__('notifications.view_message'), config('convo-lite.app_url'))
                    ->content($this->message->content);
            });
    }

    /**
     * Get the Nexmo (SMS) representation of the notification.
     */
    public function toNexmo(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\NexmoMessage)
            ->content(__('notifications.sms_message', ['sender' => $this->message->senderName]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'sender_name' => $this->message->senderName,
            'content' => $this->message->content,
        ];
    }
}
