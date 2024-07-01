<?php

namespace App\Http\Resources;

use App\Enums\StatusUserEnum;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isEdit = false;
        if ($this->is_schedule) {
            $publishedAt = Carbon::parse($this->published_at);

            if ($publishedAt >= now()) {
                $isEdit = true;
            }
        }

        $dataUsers = $this->getUser();
        return [
            'id' => $this->id,
            'type' => $this->type,
            'sender_type' => $this->sender_type,
            'title' => $this->title,
            'content' => $this->content,
            'user_ids' => $this->user_ids,
            'users' => $dataUsers['users'],
            'is_schedule' => $this->is_schedule,
            'published_at' => $this->published_at ? format_datetime($this->published_at) : null,
            'published_end_at' => $this->published_end_at ? format_datetime($this->published_end_at) : null,
            'is_edit' => $isEdit,
            'created_at' => format_datetime($this->created_at),
            'updated_at' => format_datetime($this->updated_at),
        ];
    }

    public function getUser()
    {
        $userIds = [];
        $users = [];
        if ($this->sender_type == Notice::SENDER_OPTION) {
            $notificationUsers = User::withTrashed()
                ->whereNot('status', StatusUserEnum::BLOCK->value)
                ->whereIn('id', $this->user_ids ?? [])
                ->get();

            foreach ($notificationUsers as $user) {
                $userIds[] = $user->id;
                $users[] = [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                ];
            }
        }

        return [
            'user_ids' => $userIds,
            'users' => $users,
        ];
    }
}
