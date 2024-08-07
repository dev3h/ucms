<?php

namespace App\Models\Filters;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserLogFilter implements Filters
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
        $this->filterData($query);
        return $query;
    }

    protected function filterData(Builder $query): void
    {
        $request = $this->request;
        $query->when(isset($request->search), function ($query) use ($request) {
            $query->whereLike('event', $request->input('search'));
        })
        ->when(isset($request->created_at), function ($query) use ($request) {
            $query->whereDate('created_at', $request->input('created_at'));
        });
    }
}
