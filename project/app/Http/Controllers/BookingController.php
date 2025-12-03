<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Apartment;
use App\Models\User;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
  // index
    public function index(User $user){
      $bookings = $user->bookings;
      return BookingResource::collection($bookings);
    }
    // show
    public function show(Apartment $apartment, Booking $booking){
      if($booking->apartment_id != $apartment->id){
          abort(404);
      }
      return new BookingResource($booking);
    }
    // store
    public function store(Request $request){
        $validated = $request->validate([
          'user_id' => 'required|exists:users,id',
          'apartment_id' => 'required|exists:apartments,id',
          'start_date' => 'required|date',
          'end_date' => 'required|date|after:start_date',
          'status' => 'sometimes|string',
        ]);
        
        $booking = Booking::create($validated);
        return new BookingResource($booking);
    }
    // update
    public function update(Apartment $apartment, Booking $booking, Request $request){
      $validated = $request->validate([
        'user_id' => 'sometimes|exists:users,id',
        'apartment_id' => 'sometimes|exists:apartments,id',
        'start_date' => 'sometimes|date',
        'end_date' => 'sometimes|date|after:start_date',
        'status' => 'sometimes|string',
      ]);
      $booking->update($validated);
      return new BookingResource($booking);
    }
    // destroy
    public function destroy(Apartment $apartment, Booking $booking){
        if($booking->apartment_id != $apartment->id){
            abort(404);
        }
        $booking->delete();
        return response()->noContent();
    }
}
