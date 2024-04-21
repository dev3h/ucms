<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActionResource;
use App\Models\Action;
use App\Models\Filters\ActionFilter;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Action::filters(new ActionFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate(PerPage::DEFAULT);
            return ActionResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
