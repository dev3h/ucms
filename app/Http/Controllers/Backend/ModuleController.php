<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Filters\ModuleFilter;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Module::filters(new ModuleFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate(PerPage::DEFAULT);
            return ModuleResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
