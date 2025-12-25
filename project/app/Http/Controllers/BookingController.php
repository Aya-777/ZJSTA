<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Apartment;
use App\Models\User;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Auth; 

class BookingController extends Controller
{
  // index
    public function index(){
      $user = Auth::user();
      $bookings = $user->bookings;
      return BookingResource::collection($bookings);
    }
    // show
    public function show(Booking $booking){

      if (Auth::id() !== $booking->user_id) {
        return response()->json(['message' => 'Unauthorized'], 403);
        }
      return new BookingResource($booking);
    }
    // store
    public function store(Request $request){
        $validated = $request->validate([
          'apartment_id' => 'required|exists:apartments,id',
          'start_date' => 'required|date',
          'end_date' => 'required|date|after:start_date',
          'status' => 'sometimes|string',
        ]);
        
        $apartment = Apartment::find($validated['apartment_id']);
        $apartmentBookings = $apartment->bookings()
          ->where(function($query) use ($validated) {
              $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function($query) use ($validated) {
                        $query->where('start_date', '<=', $validated['start_date'])
                              ->where('end_date', '>=', $validated['end_date']);
                    });
          })->exists();
        if($apartmentBookings){
            return response()->json(['message' => 'The apartment is already booked for the selected dates.'], 409);
        } 
        
        $validated['user_id'] = Auth::id();

        $booking = Booking::create($validated);
        return new BookingResource($booking);
    }
    // update
    public function update(Booking $booking, Request $request){
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
    public function destroy(Booking $booking){
        if (Auth::id() !== $booking->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $booking->delete();
        return response()->noContent();
    }
}
