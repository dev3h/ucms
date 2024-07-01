<?php

namespace App\Notifications;

use App\Channels\DatabaseChannel;
use App\Enums\NoticeMethod;
use App\Enums\NotificationSendType;
use App\Enums\NotificationSendTypeEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $notice;

    /**
     * Create a new notification instance.
     */
    public function __construct($notice)
    {
        $this->notice = $notice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [DatabaseChannel::class];
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

    /**
     * Get the array new column of the notification.
     *
     * @return array<string, mixed>
     */
    public function toColumn(object $notifiable): array
    {
        return [
            'notice_id' => $this->notice->id,
            'type_send' => NotificationSendTypeEnum::ADMIN->value
        ];
    }
}
