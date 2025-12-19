<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\City;
use App\Models\ApartmentImage;
// use App\Http\Resources\ImageResource;

class ReviewResource extends JsonResource
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
        'rating' => $this->rating,
        'comment' => $this->comment,
        'user_name' => $this->user->name, 
        'apartment_name' => $this->apartment->title,
        'created_at' => $this->created_at->toDateTimeString()
      ];
    }
}
