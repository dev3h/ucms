<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
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
            'module' => [
                'id' => $this?->module?->id,
                'name' => $this?->module?->name,
            ],
            "created_at" => format_date($this?->created_at),
        ];
    }
}
