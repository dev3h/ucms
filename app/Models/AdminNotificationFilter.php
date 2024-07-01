<?php

namespace App\Models;

use App\Models\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminNotificationFilter implements Filters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get query after apply filters
     *
     * @param Builder $query
     * @return Builder
     */
    public function getQuery(Builder $query): Builder
    {
        $this->filterTitle($query);
        $this->filterSenderType($query);
        $this->filterPostedTime($query);

        return $query;
    }

    protected function filterTitle(Builder $query): void
    {
        $title = $this->request->input('search');
        $query->when(isset($title), function ($query) use ($title) {
            $query->whereLike('title', $title);
        });
    }

    protected function filterSenderType(Builder $query): void
    {
        $senderType = $this->request->input('sender_type');
        $query->when(isset($senderType), function ($query) use ($senderType) {
            $query->where('sender_type', $senderType);
        });
    }

    protected function filterPostedTime(Builder $query): void
    {
        $publishedTime = $this->request->input('published_at');
        $query->when(isset($publishedTime), function ($query) use ($publishedTime) {
            $query->where('published_at', '>=', $publishedTime)
                ->orWhere(function ($subQuery) use ($publishedTime) {
                    $subQuery->whereNull('published_at')
                        ->where('created_at', '>=', $publishedTime);
                });
        });
    }
}
