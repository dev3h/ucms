<?php

namespace App\Traits;

use App\Enums\NotificationSendType;
use App\Models\Notification;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\RoutesNotifications;

trait UserNotifiableTrait
{
    use HasDatabaseNotifications;
    use RoutesNotifications;

    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * Get the entity's admin notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function adminNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->where('type_send', NotificationSendType::ADMIN->value)
            ->latest();
    }

    /**
     * Get the entity's admin public notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function adminPublicNotifications()
    {
        return $this->adminNotifications()
            ->where(function ($q) {
                $q->doesntHave('notice');
                $q->orWhereHas('notice', function ($builder) {
                    $builder->where(function ($subBuilder) {
                        $subBuilder->where('is_schedule', 0)
                            ->orWhere(function ($query) {
                                $query->where('is_schedule', 1)
                                    ->where('published_at', '<=', now());
                            });
                    })
                        ->where(function ($query) {
                            $query->whereNull('published_end_at')
                                ->orWhere('published_end_at', '>=', now());
                        });
                });
            })
            ->latest();
    }

    /**
     * Get the entity's like notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likeNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->where('type_send', NotificationSendType::USER_LIKE->value)
            ->latest();
    }

    /**
     * Get the entity's favorite notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favoriteNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->where('type_send', NotificationSendType::USER_FAVORITE->value)
            ->latest();
    }

    /**
     * Get the entity's maching notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function matchingNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->where('type_send', NotificationSendType::USER_MATCHING->value)
            ->latest();
    }
}
