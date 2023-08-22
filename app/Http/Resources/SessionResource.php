<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SessionResource extends JsonResource
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
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),
            'session_number' => $this->session_number,
            'name' => $this->name,
            'scheduled_at' => $this->scheduled_at,
            'duration' => $this->duration,
            'location' => $this->location,
            'content' => $this->content,
            'cover_image' => $this->when($this->cover_image, fn () => Storage::temporaryUrl($this->cover_image, now()->addMinutes(5))),
        ];
    }
}
