<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplatePermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this?->id,
            "name" => $this?->name,
            "code" => $this?->code,
            "subsystems" => $this->subsystems->map(function ($subsystem) {
                return [
                    "id" => $subsystem?->id,
                    "name" => $subsystem?->name,
                    "code" => $subsystem?->code,
                    "modules" => $subsystem->modules->map(function ($module) {
                        return [
                            "id" => $module?->id,
                            "name" => $module?->name,
                            "code" => $module?->code,
                            "actions" => $module->actions->map(function ($action) {
                                return [
                                    "id" => $action?->id,
                                    "name" => $action?->name,
                                    "code" => $action?->code,
                                    'checked' => $action?->checked,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];

    }
}
