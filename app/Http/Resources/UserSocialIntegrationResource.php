<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSocialIntegrationResource extends JsonResource
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
            'email' => $this?->email,
            'provider_type' => $this?->provider_type,
            'provider_id' => $this?->provider_id,
        ];
    }
}
