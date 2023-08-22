<?php

namespace App\Http\Resources;

use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quest
 */
class QuestResource extends JsonResource
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
            'compendium' => new CompendiumResource($this->whenLoaded('compendium')),
            'campaign' => new CompendiumResource($this->whenLoaded('campaign')),
            'name' => $this->name,
            'content' => $this->content,
            // todo: 'images'
            'tags' => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}
