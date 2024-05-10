<?php

namespace App\Http\Resources;

use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
//    public function temp(Request $request)
//    {
//        $data = [];
//        foreach ($this->resource as $permission) {
//            $parts = explode('-', $permission->code);
//            $systemCode = $parts[0];
//            $subsystemCode = $parts[1];
//            $moduleCode = $parts[2];
//            $actionCode = $parts[3];
//
//            $system = System::where('code', $systemCode)->first();
//            $subsystem = $system->subsystems()->where('code', $subsystemCode)->first();
//            $module = $subsystem->modules()->where('code', $moduleCode)->first();
//            $action = $module->actions()->where('code', $actionCode)->first();
//
//            // Initialize the arrays if they don't exist yet
//            if (!isset($data[$system->id])) {
//                $data[$system->id] = [
//                    'id' => $system->id,
//                    'name' => $system->name,
//                    'code' => $system->code,
//                    'subsystems' => []
//                ];
//            }
//            if (!isset($data[$system->id]['subsystems'][$subsystem->id])) {
//                $data[$system->id]['subsystems'][$subsystem->id] = [
//                    'id' => $subsystem->id,
//                    'name' => $subsystem->name,
//                    'code' => $subsystem->code,
//                    'modules' => []
//                ];
//            }
//            if (!isset($data[$system->id]['subsystems'][$subsystem->id]['modules'][$module->id])) {
//                $data[$system->id]['subsystems'][$subsystem->id]['modules'][$module->id] = [
//                    'id' => $module->id,
//                    'name' => $module->name,
//                    'code' => $module->code,
//                    'actions' => []
//                ];
//            }
//
//            // Add the action to the module
//            $data[$system->id]['subsystems'][$subsystem->id]['modules'][$module->id]['actions'][] = [
//                'id' => $action->id,
//                'name' => $action->name,
//                'code' => $action->code
//            ];
//        }
//
//        return $data;
//    }
    public function toArray(Request $request): array
    {
        // Get all system codes from permissions
        $systemCodes = $this->resource->map(function ($permission) {
            return explode('-', $permission->code)[0];
        })->unique();

        // Get all systems with these codes
        $systems = System::whereIn('code', $systemCodes)->get()->keyBy('code');

        $data = [];
        foreach ($this->resource as $permission) {
            $parts = explode('-', $permission->code);
            $systemCode = $parts[0];

            $system = $systems[$systemCode];

            // Initialize the arrays if they don't exist yet
            if (!isset($data[$system->id])) {
                $data[$system->id] = [
                    'id' => $system->id,
                    'name' => $system->name,
                    'code' => $system->code,
                    'permissions' => []
                ];
            }

            $data[$system->id]['permissions'][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'code' => $permission->code
            ];
        }

        return $data;
    }
}
