<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Apartment;
use App\Models\User;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Auth; 
use App\Services\FcmService;
use App\Notifications\NewBookingNotification;
use App\Notifications\UpdateBookingNotification;
use App\Notifications\RequestUpdateBookingNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Filters\BookingFilter;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
  // index
      public function index(Request $request){
      $user = Auth::user();

    // filter bookings
      $query = Booking::where('user_id', $user->id);
      $query = BookingFilter::apply($query ,$request);
      $bookings = $query->with(['apartment','user'])
                  ->orderBy('start_date', 'desc')
                  ->paginate(15);

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
    public function store(Request $request, FcmService $fcm){

      if(auth()->user()->id != $request->user_id){
        abort(403);
      }

      return DB::transaction(function () use ($request) {
      // validate data
        $validated = $request->validate([
          'apartment_id' => 'required|exists:apartments,id',
          'start_date' => 'required|date',
          'end_date' => 'required|date|after:start_date',
          'status' => 'sometimes|string|default:pending',
        ]);
        
      // find overlapping bookings
        $apartment = Apartment::find($validated['apartment_id']);
        $apartmentBookings = $apartment->bookings()
          ->where('status','confirmed')
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
      });
    }

    // sending request update (renter)
    public function update(Booking $booking, Request $request){
      $user = auth()->user();
      if($user->id != $booking->user_id){
        abort(403);
      }

    // validate data
      $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
      ]);

    // request update booking with pending modifications
      $booking->update([
        'pending_modifications' => [
          'start_date' => $request->start_date,
          'end_date' => $request->end_date,
        ],
        'status' => 'update_pending'
      ]);
      $booking->save();


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

    public function cancel(Booking $booking){
        $user = auth()->user(); 
        if($user->id !== $booking->user_id){
            abort(403);
        }
        $booking->status = 'cancelled';
        $booking->save();
        return response()->noContent();
    }

  // update status confirm or reject by (owner)
  public function updateStatus(Request $request, $id, FcmService $fcm){   

    $request->validate(['status' => 'required|in:confirmed,rejected']);
    
    $booking = Booking::findOrFail($id);
    $owner = $booking->apartment->user;
    $decision = $request->status;
    
    if ($request->status != 'completed' && auth()->id() !== $owner->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    // confirm update booking 
    if (!empty($booking->pending_modifications)&& $decision === 'confirmed') {
        $booking->update([
            'start_date' => $booking->pending_modifications['start_date'],
            'end_date'   => $booking->pending_modifications['end_date'],
            'pending_modifications' => null,
            'status' => $decision
        ]);
      }else{
        $booking->update([
          'pending_modifications' => null,
          'status' => $decision
        ]);
      }

    // Reject all overlapping pending bookings
    $apartment = $booking->apartment;
    $apartmentBookings = $apartment->bookings()
      ->where('status','pending')
      ->where(function($query) use ($booking) {
          $query->whereBetween('start_date', [$booking->start_date , $booking->end_date])
            ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date])
            ->orWhere(function($query) use ($booking) {
              $query->where('start_date', '<=', $booking->start_date)
                    ->where('end_date', '>=', $booking->end_date);
                });
      })->get();

      if(!empty($apartmentBookings)){
        foreach ($apartmentBookings as $b) {
          $b->status = 'rejected';
          $b->save();
        }
  }
      

    return response()->json(['message' => 'Booking updated successfully', 'status' => $decision]);
  }

}
