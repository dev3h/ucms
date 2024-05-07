<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Filters\ModuleFilter;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view', User::class);
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

    public function store(ModuleRequest $request)
    {
        $this->authorize('create', User::class);
        try {
            $data = $request->validated();
            Module::create($data);
            return $this->sendSuccessResponse(null, __('Created successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function update($id, ModuleRequest $request)
    {
        $this->authorize('update', User::class);
        $data = $request->validated();
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $module->update($data);
            return $this->sendSuccessResponse(null, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('view', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            return $this->sendSuccessResponse(ModuleResource::make($module));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $module->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
