<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Booking;
use App\Models\ApartmentImage;
use App\Models\City;

class Apartment extends Model
{
    /** @use HasFactory<\Database\Factories\ApartmentFactory> */
    use HasFactory;

    protected $guarded = [];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function images()
    {
        return $this->hasMany(ApartmentImage::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function reviews(){
      return $this->hasMany(Review::class);
    }
    // To get the average rating: $apartment->reviews()->avg('rating');

    // Filters
      public function scopeCity($query, $city){
        return $query->where('city',$city);
      }

      public function scopeRooms($query, $rooms){
        return $query->where('rooms', $rooms);
      }

      public function scopeMinRooms($query, $rooms){
        return $query->where('rooms', '>=' , $rooms);
      }

      public function scopeMaxPrice($query, $price){
        return $query->where('pricePerMonth', '<=', $price);
      }

      public function scopeMinPrice($query, $price){
        return $query->where('pricePerMonth', '>=', $price);
      }

      public function scopeFurnished($query, $furnished){
        return $query->where('furnished', $furnished);
      }

      public function scopeRentalType($query, $type){
        return $query->where('rentalType', $type);
      }

      public function scopeArea($query, $area){
        return $query->where('area', '>=', $area);
      }

}
