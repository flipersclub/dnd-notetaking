<?php

namespace App\Http\Resources\Compendium;

use App\Http\Resources\CompendiumResource;
use App\Http\Resources\TagResource;
use App\Models\Compendium\Plane;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Plane
 */
class PlaneResource extends JsonResource
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
            'name' => $this->name,
            'content' => $this->content,
            // todo: 'images'
            'tags' => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}