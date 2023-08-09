<?php

namespace App\Http\Resources\Compendium\Location;

use App\Http\Resources\SettingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'setting' => new SettingResource($this->whenLoaded('setting')),
            'parent' => new LocationResource($this->whenLoaded('parent')),
            'name' => $this->name,
            'type' => new TypeResource($this->type),
            'size' => new SizeResource($this->size),
            'content' => $this->content,
            'demonym' => $this->demonym,
            'population' => $this->population,
            'government' => new GovernmentTypeResource($this->whenLoaded('governmentType')),
            // todo: 'aliases'
            // todo: 'services'
            // todo: 'maps'
            // todo: 'species'
            // todo: 'characters'
            // todo: 'encounters'
            // todo: 'natural_resources'
            // todo: 'languages'
            // todo: 'images'
        ];
    }
}
