<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubSystemRequest;
use App\Http\Resources\SubSystemResource;
use App\Models\Filters\SubSystemFilter;
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
        $this->authorize('create', User::class);
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
        $this->authorize('update', User::class);
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
        $this->authorize('view', User::class);
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
        $this->authorize('delete', User::class);
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
}
