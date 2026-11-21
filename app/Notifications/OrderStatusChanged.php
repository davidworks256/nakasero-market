<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $event,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        $title = match ($this->event) {
            'requested' => 'New order request from '.$this->order->buyer?->name,
            'accepted' => 'Your order was accepted',
            'declined' => 'Your order was declined',
            'completed' => 'Order marked as completed',
            'cancelled' => 'Order cancelled',
            default => 'Order update',
        };

        return (new MailMessage)
            ->subject($title.' · Nakasero Market')
            ->greeting("Hello {$notifiable->name},")
            ->line($title.'.')
            ->line("Order #{$this->order->id} — {$this->order->quantity} × ".($this->order->listing?->title ?? 'listing').' at UGX '.number_format($this->order->unit_price).'.')
            ->line('Sign in to see the current status.')
            ->action('Open dashboard', route('dashboard'))
            ->line('Prices are agreed and paid directly between buyer and farmer.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'event' => $this->event,
            'title' => "Order #{$this->order->id} {$this->event}",
        ];
    }
}
