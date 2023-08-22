<?php

namespace App\Http\Resources;

use App\Models\Encounter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Encounter
 */
class EncounterResource extends JsonResource
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
            'campaign' => new CompendiumResource($this->whenLoaded('campaign')),
            'compendium' => new CompendiumResource($this->whenLoaded('compendium')),
            'name' => $this->name,
            'content' => $this->content,
            // todo: 'images'
            'tags' => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}
