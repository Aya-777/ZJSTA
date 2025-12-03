<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\City;
use App\Models\ApartmentImage;
// use App\Http\Resources\ImageResource;

class BookingResource extends JsonResource
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
        'user_id' => $this->user_id,
        'apartment_id' => $this->apartment_id,
        'start_date' => $this->start_date,
        'end_date' => $this->end_date,
        'status' => $this->status,
      ];
    }
}
