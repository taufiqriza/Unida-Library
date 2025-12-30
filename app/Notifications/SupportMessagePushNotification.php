<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class SupportMessagePushNotification extends Notification
{
    public function __construct(
        public string $memberName,
        public string $topic,
        public int $roomId
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $topicLabels = [
            'unggah' => 'Unggah Mandiri',
            'plagiasi' => 'Cek Plagiasi',
            'bebas' => 'Bebas Pustaka',
            'pinjam' => 'Peminjaman',
            'lainnya' => 'Lainnya',
        ];
        
        return (new WebPushMessage)
            ->title('Pesan Support Baru')
            ->icon('/images/logo-icon.png')
            ->body("{$this->memberName} - " . ($topicLabels[$this->topic] ?? 'Support'))
            ->data(['url' => '/staff/chat?support=' . $this->roomId]);
    }
}
