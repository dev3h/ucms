<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubSystemResource extends JsonResource
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
            'system' => [
                'id' => $this?->system?->id,
                'name' => $this?->system?->name,
            ],
            "created_at" => format_date($this?->created_at),
        ];
    }
}
