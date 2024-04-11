<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubSystemResource;
use App\Models\Filters\SubSystemFilter;
use App\Models\SubSystem;
use Illuminate\Http\Request;

class SubSystemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = SubSystem::filters(new SubSystemFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate(PerPage::DEFAULT);
            return SubSystemResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
