<?php

namespace App\Http\Resources\Compendium;

use App\Http\Resources\CompendiumResource;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterResource extends JsonResource
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
            'age' => $this->age,
            'gender' => $this->gender,
            'species' => new SpeciesResource($this->whenLoaded('species')),
            'content' => $this->content,
            // todo: 'encounters'
            // todo: 'natural_resources'
            // todo: 'languages'
            // todo: 'images'
            'tags' => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}
