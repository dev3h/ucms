<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubSystemRequest;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\SubSystemResource;
use App\Models\Filters\ModuleFilter;
use App\Models\Filters\SubSystemFilter;
use App\Models\Module;
use App\Models\SubSystem;
use App\Models\User;
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
    public function store(SubSystemRequest $request)
    {
        try {
            $data = $request->validated();
            SubSystem::create($data);
            return $this->sendSuccessResponse(null, __('Created successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function update($id, SubSystemRequest $request)
    {
        $data = $request->validated();
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $subSystem->update($data);
            return $this->sendSuccessResponse(null, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            return $this->sendSuccessResponse(SubSystemResource::make($subSystem));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $subSystem->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getAllModuleOfSubSystem($id, Request $request)
    {
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $modules = $subSystem->modules()->filters(new ModuleFilter($request))
                ->orderBy('created_at', 'desc')->paginate(PerPage::DEFAULT);

            return ModuleResource::collection($modules)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function restModule($id)
    {
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $modules = Module::all();
            $moduleOfSubSystem = $subSystem->modules;
            $diffModule = $modules->diff($moduleOfSubSystem);
            $data = ModuleResource::collection($diffModule);
            return $this->sendSuccessResponse($data);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function addExtraModule($id, Request $request)
    {
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $moduleIds = $request->input('ids');
            $subSystem->modules()->attach($moduleIds);
            return $this->sendSuccessResponse(null, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function removeModule($id, $module_id)
    {
        try {
            $subSystem = SubSystem::find($id);
            if(!$subSystem) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $subSystem->modules()->detach($module_id);
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
