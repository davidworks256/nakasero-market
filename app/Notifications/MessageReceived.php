<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReceived extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New message from '.$this->message->sender->name.' · Nakasero Market')
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message->sender->name.' sent you a message:')
            ->line('"'.str($this->message->body)->limit(140).'"')
            ->action('Read message', route('inbox.show', $this->message->conversation_id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'title' => 'New message from '.$this->message->sender->name,
        ];
    }
}
