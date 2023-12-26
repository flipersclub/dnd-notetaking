<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'thumbnail' => Storage::url("images/$this->id/thumbnail-$this->name.$this->extension"),
            'original' => Storage::url("images/$this->id/original-$this->name.$this->extension")
        ];
    }
}
