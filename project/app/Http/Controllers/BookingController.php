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
    public function index(){
      $user = User::find(1); // Temporarily hardcoded for testing use auth()->user();
      $bookings = $user->bookings;
      return BookingResource::collection($bookings);
    }
    // show
    public function show(Booking $booking){
      $user = User::find(2); // Temporarily hardcoded for testing use auth()->user();
      if($user->id !== $booking->user_id){
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
        $user = User::find(2); // Temporarily hardcoded for testing use auth()->user();
        if($user->id !== $booking->user_id){
            abort(404);
        }
        $booking->delete();
        return response()->noContent();
    }
}
