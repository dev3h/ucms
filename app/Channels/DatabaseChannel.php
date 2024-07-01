<?php

namespace App\Channels;

use App\Enums\NotificationSendType;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use RuntimeException;

class DatabaseChannel extends IlluminateDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function send($notifiable, Notification $notification)
    {
        $newColumns = $this->appendNewColumns($notifiable, $notification);

        $notificationModel = $notifiable->routeNotificationFor('database')->create([
            'id'      => $notification->id,
            'type'    => get_class($notification),
            'data'    => $this->getData($notifiable, $notification),
            'read_at' => null,
            ...$newColumns
        ]);

        // push firebase notification
        self::pushFirebaseNotification($notifiable, $notificationModel);

        return $notificationModel;
    }

    /**
     * Push firebase notification
     *
     * @param mixed $notifiable
     * @param mixed $notification
     * @return void
     */
    private static function pushFirebaseNotification(mixed $notifiable, mixed $notification): void
    {
        $dataNotification = self::getDataNotification($notification);

        // push firebase notification job
        PushNotificationJob::dispatch($notifiable->id, $dataNotification);
    }

    /**
     * Get the data for the notification.
     *
     * @param mixed $notification
     * @return array
     */
    private static function getDataNotification(mixed $notification): array
    {
        $typeSend = NotificationSendType::tryFrom($notification->type_send)->name;
        if ($notification->notice) {
            $title = $notification->notice->title;
            $content = $notification->notice->content;
        } else {
            $title = $notification->data['title'] ?? null;
            $content = $notification->data['message'] ?? null;
        }

        $data = $notification->data;
        if (isset($data['user_id']) && $notification->type_send != NotificationSendType::ADMIN->value) {
            $user = User::whereId($data['user_id'])->first();
            $userData = get_basic_user_info($user);
            $title = $userData['nickname'];
            $iconUrl = $userData['avatar'];
        } else {
            $iconUrl = env('APP_URL') . '/images/logo/favicon.svg';
        }

        return [
            'title' => $title ?? null,
            'content' => $content ?? null,
            'icon_url' => $iconUrl ?? null,
            'type_send' => "NOTIFICATION_{$typeSend}",
            'user' => $userData ?? null,
            'notification' => [
                'id' => $notification->id,
                'type_send' => $notification->type_send,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ]
        ];
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function appendNewColumns(mixed $notifiable, mixed $notification)
    {
        if (method_exists($notification, 'toColumn')) {
            return $notification->toColumn($notifiable) ?? [];
        }

        throw new RuntimeException('Notification is missing toColumn method.');
    }
}
