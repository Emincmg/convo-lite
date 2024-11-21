<?php

namespace Emincmg\ConvoLite\Notifications;

use Emincmg\ConvoLite\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;
    /**
     * Create a new notification instance.
     */
    public function __construct(public Message $message, public int $userId)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array|string
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
            ->greeting(__('notifications.greeting', ['name' => "{$notifiable->first_name} {$notifiable->last_name}"]))
            ->line(__('notifications.new_message_line', ['sender' => $this->message->senderName]))
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
            'body' => $this->message->body,
            'read_by' => $this->message->readBy,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->userId)];
    }

    /**
     * The name of the queue on which to place the broadcasting job.
     */
    public function viaQueues(): array
    {
        return config('convo-lite.queues');
    }

    /**
     * The event's broadcast queue.
     */
    public function broadcastQueue(): string
    {
        return config('convo-lite.queues.broadcast');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'created_at' => $this->message->created_at,
            'body' => $this->message->body,
            'read_by' => $this->message->readBy->map(fn($user) => $user->name),
            'conversation' => [
                'id' => $this->message->conversation->id,
                'title' => $this->message->conversation->title,
            ],
        ];
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
                $attachment
                    ->title(__('notifications.view_message'), config('convo-lite.app_url'))
                    ->content($this->message->body);
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
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'sender_name' => $this->message->senderName,
            'body' => $this->message->body,
        ];
    }
}
