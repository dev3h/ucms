<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            "last_seen" => Carbon::parse($this?->last_seen)->diffForHumans(),
            "activity" => $this?->last_seen >= now()->subMinutes(2) ? "online" : "offline",
            "created_at" => format_date($this?->created_at),
        ];
    }
}
