<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ApartmentFilter
{
    /**
     * Applies filtering logic to the given Eloquent query builder based on the request.
     * * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public static function apply(Builder $query, Request $request): Builder
    {
        // City Filter
        if ($request->filled('city')) {
            $query->where('city_id', $request->city);
        }
        
        // Rooms Filters
        if ($request->filled('rooms')) {
            $query->where('numberOfRooms' ,(int) $request->rooms); 
        }
        if ($request->filled('min_rooms')) {
            $query->where('numberOfRooms' , '>=' ,(int) $request->min_rooms);
        }
        if ($request->filled('max_rooms')) {
            $query->where('numberOfRooms' , '<=' ,(int) $request->max_rooms);
        }
        
        // Price Filters
        if ($request->filled('max_price')) {
            $query->where('pricePerMonth', '<=' , (int) $request->max_price);
        }
        if ($request->filled('min_price')) {
            $query->where('pricePerMonth' , '>=' ,(int) $request->min_price);
        }
        
        // Furnished Filter
        if ($request->filled('furnished')) {
            $query->where('furnished' ,$request->furnished); 
        }

        // Rental Type Filter
        if ($request->filled('rental_type')) {
            $query->where('rentalType' ,$request->rental_type);
        }

        // Area Filter
        if ($request->filled('min_area')) {
            $query->where('area' , '>=' ,(int) $request->min_area);
        }
        
        return $query;
    }
}