<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'imageable_type' => $this->imageable_type,
            'imageable_id' => $this->imageable_id,
            'path' => $this->path,
            'created_at' => $this->created_at,
        ];
    }
}
