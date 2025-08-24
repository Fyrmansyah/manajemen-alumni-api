<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Append computed photo_url for clients (if model has accessor)
        if (method_exists($this->resource, 'getPhotoUrlAttribute')) {
            $data['photo_url'] = $this->resource->photo_url;
        }

        return $data;
    }
}
