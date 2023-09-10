<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\arrayHasKey;

class CompendiumResource extends JsonResource
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
            'cover_image' => $this->when($this->cover_image, fn () => Storage::temporaryUrl($this->cover_image, now()->addMinutes(5))),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'hasLocations' => $this->when(!is_null($this->locations_count), $this->locations_count),
            'hasCharacters' => $this->when(!is_null($this->characters_count), $this->characters_count),
            'hasSpecies' => $this->when(!is_null($this->species_count), $this->species_count),
            'hasItems' => $this->when(!is_null($this->items_count), $this->items_count),
            'hasConcepts' => $this->when(!is_null($this->concepts_count), $this->concepts_count),
            'hasFactions' => $this->when(!is_null($this->factions_count), $this->factions_count),
        ];
    }
}
