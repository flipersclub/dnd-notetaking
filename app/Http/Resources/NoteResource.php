<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\arrayHasKey;

class NoteResource extends JsonResource
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
            'notebook' => new NotebookResource($this->whenLoaded('notebook')),
            'cover_image' => $this->when($this->cover_image, fn () => Storage::temporaryUrl($this->cover_image, now()->addMinutes(5))),
        ];
    }
}
