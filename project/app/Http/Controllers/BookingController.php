<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Apartment;
use App\Models\User;
use App\Http\Resources\BookingResource;
use App\Services\FcmService;
use App\Notifications\NewBookingNotification;
use App\Notifications\UpdateBookingNotification;
use App\Notifications\RequestUpdateBookingNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Filters\BookingFilter;

class BookingController extends Controller
{
  // index
    public function index(Request $request){
      $user = User::find(2); // Temporarily hardcoded for testing use auth()->user();
      $query = Booking::where('user_id', $user->id);
      $query = BookingFilter::apply($query ,$request);
      $bookings = $query->with(['apartment','user'])
                  ->orderBy('start_date', 'desc')
                  ->paginate(15);
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
    public function store(Request $request, FcmService $fcm){
        $validated = $request->validate([
          'user_id' => 'required|exists:users,id',
          'apartment_id' => 'required|exists:apartments,id',
          'start_date' => 'required|date',
          'end_date' => 'required|date|after:start_date',
          'status' => 'sometimes|string|default:pending',
        ]);
        
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
        
        $booking = Booking::create($validated);

        $owner = $apartment->user;
        $owner->notify(new NewBookingNotification($booking));
        if ($owner->fcm_token) {
        try {
            $fcm->sendNotification(
                $owner->fcm_token,
                'New Rental Request!',
                'Someone wants to rent your apartment. Please approve or reject.',
                ['booking_id' => (string)$booking->id, 'action' => 'review_request'] 
            );
        } catch (\Exception $e) {
            // Log it, but don't stop the booking from succeeding
            \Log::error("Push failed: " . $e->getMessage());
        }
      }

        return new BookingResource($booking);
    }

    // update
    public function update(Booking $booking, Request $request){
      $user = User::find(2); // Temporarily hardcoded for testing use auth()->user();
      if($user->id != $booking->user_id){
        abort(403);
      }
      $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
      ]);

      $booking->update([
        'pending_modifications' => [
          'start_date' => $request->start_date,
          'end_date' => $request->end_date,
        ],
        'status' => 'pending'
      ]);

      $apartment = Apartment::find($booking->apartment_id);
      $owner = $apartment->user;
      $owner->notify(new RequestUpdateBookingNotification($booking));
      if ($owner->fcm_token) {
        try {
            $fcm->sendNotification(
                $owner->fcm_token,
                'Update Booking Request',
                'Someone wants to update their booking. Please approve or reject.',
                ['booking_id' => (string)$booking->id, 'action' => 'update_request'] 
            );
        } catch (\Exception $e) {
            \Log::error("Push failed: " . $e->getMessage());  
        }
      }

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

  public function updateStatus(Request $request, $id, FcmService $fcm){      
    $request->validate(['status' => 'required|in:confirmed,rejected,completed']);
    
    $booking = Booking::findOrFail($id);
    $owner = $booking->apartment->user;
    $decision = $request->status;
    
    // if ($request->status != 'completed' && auth()->id() !== $owner->id) {
    //   return response()->json(['message' => 'Unauthorized'], 403);
    // }
    

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

    // Notify the renter
    $renter = $booking->user;
    $title = $decision == 'confirmed' ? 'Booking Approved!' :
    ($decision == 'rejected' ? 'Booking Rejected!' :
    'Booking Completed!');
    $message = $decision == 'confirmed' 
      ? "Pack your bags! Your stay at {$booking->apartment->title} is confirmed."
      : ( $decision == 'rejected' 
          ? "We're sorry to inform you that your booking request for {$booking->apartment->title} has been rejected."
          : "Thank you for staying at {$booking->apartment->title}! We hope you had a great time.");

    $this->notifyRenter($renter, $booking, $title, $message);


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
        foreach($apartmentBookings as $b){
          $b->update(['status' => 'rejected']);

          // Notify the renter
          $renter = $b->user;
          $title = 'Your Booking has been rejected.';
          $message = 'Sorry the apartment is already booked for the selected dates( ' . $b->start_date . ' to ' . $b->end_date . ' )';

          $this->notifyRenter($renter, $b, $title, $message);
        }
      }
      

    return response()->json(['message' => 'Booking updated successfully', 'status' => $decision]);
  }

     protected function notifyRenter($renter, $booking, $title, $message){
      // Save to DB history for Renter
      $renter->notify(new UpdateBookingNotification($booking));

      // Send Push to Renter
      if ($renter->fcm_token) {
        $fcm->sendNotification($renter->fcm_token, $title, $message, [
            'booking_id' => (string)$booking->id,
            'status' => $decision
        ]);
      }
    }

}
