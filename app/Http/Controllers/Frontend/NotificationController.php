<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        return Inertia::render('Notification/Index');
    }

    public function create()
    {
        return Inertia::render('Notification/Form');
    }

    public function show(string $id)
    {
        $notice = Notice::findOrFail($id);

        return Inertia::render('Notification/Show', [
            'notice_id' => $notice->id,
        ]);
    }

    public function update(string $id)
    {
        $notice = Notice::whereId($id)
            ->where('is_schedule', 1)
            ->firstOrFail();

        if ($notice->is_schedule) {
            $publishedAt = Carbon::parse($notice->published_at);

            if ($publishedAt <= now()) {
                abort(404);
            }
        }

        return Inertia::render('Notification/Form', [
            'notice_id' => $notice->id,
        ]);
    }
}
