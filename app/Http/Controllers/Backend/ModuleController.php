<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;
use App\Http\Resources\ActionResource;
use App\Http\Resources\ModuleResource;
use App\Models\Action;
use App\Models\Filters\ActionFilter;
use App\Models\Filters\ModuleFilter;
use App\Models\Module;
use App\Models\SubSystem;
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

    public function getAllActionOfModule($id, Request $request)
    {
        $this->authorize('view', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $actions = $module->actions()->filters(new ActionFilter($request))
                ->orderBy('created_at', 'desc')->paginate(PerPage::DEFAULT);;

            return ActionResource::collection($actions)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function restAction($id)
    {
        $this->authorize('view', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $actions = Action::all();
            $actionOfModule = $module->actions;
            $diffAction = $actions->diff($actionOfModule);
            $data = ActionResource::collection($diffAction);
            return $this->sendSuccessResponse($data);

        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function addExtraAction($id, Request $request)
    {
        $this->authorize('update', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $actionIds = $request->input('ids');
            $module->actions()->attach($actionIds);
            return $this->sendSuccessResponse(null, __('Added successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function removeAction($id, $action_id)
    {
        $this->authorize('delete', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $module->actions()->detach($action_id);
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getAllSubSystemOfModule($id)
    {
        $this->authorize('view', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $subSystems = $module->subSystems;
            return $this->sendSuccessResponse($subSystems);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function restSubSystem($id)
    {
        $this->authorize('view', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $subSystems = $module->subSystems;
            $allSubSystems = SubSystem::all();
            $diffSubSystems = $allSubSystems->diff($subSystems);
            return $this->sendSuccessResponse($diffSubSystems);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function addExtraSubSystem($id, Request $request)
    {
        $this->authorize('update', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $subSystemIds = $request->input('ids');
            $module->subSystems()->attach($subSystemIds);
            return $this->sendSuccessResponse(null, __('Added successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function removeSubSystem($id, $subsystem_id)
    {
        $this->authorize('delete', User::class);
        try {
            $module = Module::find($id);
            if(!$module) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $module->subSystems()->detach($subsystem_id);
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
