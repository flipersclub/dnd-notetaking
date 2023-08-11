<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'content' => $this->content,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'level' => $this->level,
            'active' => $this->active,
            'visibility' => $this->visibility,
            'player_limit' => $this->player_limit,
            'cover_image' => $this->when($this->cover_image, fn () => Storage::temporaryUrl($this->cover_image, now()->addMinutes(5))),
            'gameMaster' => new UserResource($this->whenLoaded('gameMaster')),
            'system' => new SystemResource($this->whenLoaded('system')),
            'compendium' => new CompendiumResource($this->whenLoaded('compendium')),
            'tags' => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}
