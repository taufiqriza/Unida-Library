<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ChatPushNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public int $roomId
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon('/images/logo-icon.png')
            ->body($this->body)
            ->badge('/images/logo-icon.png')
            ->tag('chat-' . $this->roomId)
            ->renotify()
            ->data(['url' => '/staff?open_chat=' . $this->roomId]);
    }
}
