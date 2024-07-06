<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Enums\StatusUserEnum;
use App\Exceptions\ForbiddenException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NoticeResource;
use App\Http\Resources\UserNotificationResource;
use App\Models\AdminNotificationFilter;
use App\Models\Notice;
use App\Models\Notification as ModelsNotification;
use App\Models\User;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notices = Notice::query()
            ->filters(new AdminNotificationFilter($request))
            ->latest()
            ->paginate($request->limit ?? PerPage::DEFAULT);

        return NoticeResource::collection($notices)
            ->additional(["status_code" => 200, "message" => __('Get list successfully')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $senderType = $data['sender_type'] ?? Notice::SENDER_ALL;
            $userIds = $data['user_ids'] ?? null;

            if ($data['is_schedule'] == false) {
                $data['is_send'] = true;
            }
            $notice = Notice::create($data);

            if (!$notice->is_schedule) {
                if ($senderType == Notice::SENDER_ALL) {
                    $users = User::all();
                } else {
                    $users = User::whereIn('id', $userIds)->get();
                }
                Notification::send($users, new AdminNotification($notice));
            }

            DB::commit();
            return $this->sendSuccessResponse(true, __('Created successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notice = Notice::findOrFail($id);

        return NoticeResource::make($notice)
            ->additional(["status_code" => 200, "message" => __('Get successfully')]);
    }

    /**
     * Update the specified resource in storage.
     * @throws ForbiddenException
     */
    public function update(UpdateNotificationRequest $request, string $id)
    {
        $data = $request->validated();
        $notice = Notice::findOrFail($id);

        if ($notice->is_schedule) {
            $publishedAt = Carbon::parse($notice->published_at);
            if ($publishedAt <= now()) {
                throw new ForbiddenException();
            }
        }

        DB::beginTransaction();
        try {
            $senderType = $data['sender_type'] ?? Notice::SENDER_ALL;
            $userIds = $data['user_ids'] ?? null;

            if ($data['is_schedule'] == false) {
                $data['is_send'] = true;
            }
            $notice->update($data);

            if (!$notice->is_schedule) {
                if ($senderType == Notice::SENDER_ALL) {
                    $users = User::all();
                } else {
                    $users = User::whereIn('id', $userIds)->get();
                }
                Notification::send($users, new AdminNotification($notice));
            }

            DB::commit();
            return $this->sendSuccessResponse(true, __('Updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notice = Notice::findOrFail($id);

        $notice->delete();

        foreach ($notice->notifications as $notification) {
            $notification->delete();
        }

        return $this->sendSuccessResponse(true, __('Deleted successfully.'));
    }

    public function membersCanAdd(Request $request)
    {
        $listSelects = $request->user_ids ?? [];
        $noticeId = $request->notice_id;
        $listSelects = array_filter($listSelects, function ($value) {
            return !is_null($value);
        });

        $userIds = [];
        if ($noticeId) {
            $notifications = ModelsNotification::query()
                ->with('user')
                ->whereJsonContains('data->notice_id', $noticeId)
                ->get();
            $userIds = $notifications->pluck('notifiable_id')->toArray();
        }

        $arrayUserIds = array_merge($userIds, $listSelects);

        $users = User::query()
            ->whereNotIn('id', $arrayUserIds)
//            ->where('status', StatusUserEnum::ACTIVE->value)
            ->when(isset($request->name), function ($query) use ($request) {
                $query->whereLike('name', $request->name);
            })
            ->orderBy('name', 'asc')
            ->paginate(PerPage::DEFAULT);

        return UserNotificationResource::collection($users)
            ->additional(["status_code" => 200, "message" => __('Get list successfully')]);
    }
}
