<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NotebookResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'hasNotes' => $this->when(!is_null($this->notes_count), $this->notes_count),
        ];
    }
}
