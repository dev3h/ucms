<?php

namespace App\Console\Commands;

use App\Models\Notice;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class PublishNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'noti:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish notification in schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Logic to publish notification
        $notices = Notice::query()->where('is_schedule', true)->where('published_at', '<=', now())->get();
        if($notices->count() > 0) {
            foreach ($notices as $notice) {
                if ($$notices?->sender_type == Notice::SENDER_ALL) {
                    $users = User::all();
                } else {
                    $users = User::whereIn('id', $notice?->user_ids)->get();
                }
                Notification::send($users, new AdminNotification($notice));
            }
        }
    }
}
