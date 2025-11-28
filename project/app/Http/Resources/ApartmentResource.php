<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
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
        'title' => $this->title,
        'description' => $this->description,
        'price_per_month' => number_format($this->pricePerMonth, 2),
        'rooms' => $this->rooms,
        'city' => new CityResource($this->whenLoaded('city')),
        'images' => ImageResource::collection($this->whenLoaded('images')),
        'owner_id' => $this->ownerID,
      ];
    }
}
