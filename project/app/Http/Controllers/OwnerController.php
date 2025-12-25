<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\BookingResource;

class OwnerController extends Controller
{
    public function getMyApartments(Request $request)
    {
        $ownerId = auth()->id();

        $apartments = Apartment::where('user_id', $ownerId)
            ->with(['bookings' => function($query) {
                $query->orderBy('start_date', 'desc');
                $query->with('user');
            }])
            ->get();

        return ApartmentResource::collection($apartments);

    }

    public function getApartmentBookings($id) {
        $apartment = Apartment::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $bookings = $apartment->bookings()->with('user')->paginate(10);
        
      return BookingResource::collection($bookings);
    }
}
