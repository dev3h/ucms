<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "email" => $this?->email,
            "role_name" => $this?->getRoleNames()->first(),
            "role_id" => $this?->roles->first()?->id,
            "created_at" => format_date($this?->created_at),
        ];
    }
}
