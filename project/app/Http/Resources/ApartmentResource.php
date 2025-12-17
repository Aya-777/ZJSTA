<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\City;
use App\Models\ApartmentImage;
// use App\Http\Resources\ImageResource;

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
        'number_of_rooms' => $this->numberOfRooms,
        'city' => City::find($this->city_id),
        'images' => $this->whenLoaded('images'),
        'owner_id' => $this->user_id,
        'furnished' => $this->furnished,
        'rentalType' => $this->rentalType,
        'area' => $this->area,
      ];
    }
}
