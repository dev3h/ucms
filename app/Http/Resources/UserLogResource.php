<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLogResource extends JsonResource
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
            "actor" => $this?->user?->name,
            "event" => $this?->event,
            "target" => $this?->auditable_type,
            "created_at" => format_datetime($this?->created_at),
        ];
    }
}
