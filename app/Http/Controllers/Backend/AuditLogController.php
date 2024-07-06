<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserLogResource;
use App\Models\Filters\UserLogFilter;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Audit::filters(new UserLogFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate($request->limit ?? PerPage::DEFAULT);
            return UserLogResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
