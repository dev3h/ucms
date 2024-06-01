<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $base = [
            "id" => $this?->id,
            "name" => $this?->name,
            "code" => $this?->code,
        ];
        if($request->routeIs('admin.api.user.role.all-permission')) {
            return array_merge($base, [
                "is_direct" => $this?->pivot?->is_direct,
                "checked" => $this?->pivot?->is_direct === 3,
            ]);
        }
        return [
            "id" => $this?->id,
            "name" => $this?->name,
            "code" => $this?->code,
        ];
    }
}
