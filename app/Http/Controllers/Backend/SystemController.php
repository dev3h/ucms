<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemRequest;
use App\Http\Resources\SystemResource;
use App\Models\Filters\SystemFilter;
use App\Models\System;
use App\Models\User;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = System::filters(new SystemFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate($request->limit ?? PerPage::DEFAULT);
            return SystemResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function store(SystemRequest $request)
    {
        $this->authorize('create', User::class);
        try {
            $data = $request->validated();
            System::create($data);
            return $this->sendSuccessResponse(null, __('Created successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function update($id, SystemRequest $request)
    {
        $this->authorize('update', User::class);
        $data = $request->validated();
        try {
            $system = System::find($id);
            if(!$system) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $system->update($data);
            return $this->sendSuccessResponse(null, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('view', User::class);
        try {
            $system = System::find($id);
            if(!$system) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            return $this->sendSuccessResponse(SystemResource::make($system));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete', User::class);
        try {
            $system = System::find($id);
            if(!$system) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $system->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
