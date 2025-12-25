<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Apartment;
use App\Models\User;

class ReviewController extends Controller
{

  public function index(Request $request){
    $request->validate([
      'apartment_id' => 'required|exists:apartments,id',
    ]);
    $reviews = Review::where('apartment_id', $request->apartment_id)
        ->with('user') // Eager load the user who wrote the review
        ->latest()     // Show newest reviews first
        ->get();
    return ReviewResource::collection($reviews);
  }
  
  public function show(Review $review, Request $request){
    $review->load(['user', 'apartment']);
    return new ReviewResource($review);
  }

    public function store(Request $request) {
    $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string'
    ]);

    $booking = Booking::where('id', $request->booking_id)
    ->where('user_id', auth()->id())
    ->where('status', 'completed')
    ->firstOrFail();
    
    $review = Review::create([
      'user_id' => auth()->id(),
      'apartment_id' => $booking->apartment_id,
      'booking_id' => $booking->id,
      'rating' => $request->rating,
      'comment' => $request->comment,
    ]);
    return new ReviewResource($review);
  }
  
  public function update(Review $review, Request $request){
    $user = auth()->user();
    if($user->id != $review->user_id){
      abort(403);
    }
    $validated = $request->validate([
      'booking_id' => 'sometimes|exists:bookings,id',
      'rating' => 'sometimes|integer|min:1|max:5',
      'comment' => 'nullable|string'
    ]);
    $review->update($validated);
    return new ReviewResource($review);
  }
  public function delete(Review $review){
    $user = auth()->user();
    if($user->id != $review->user_id){
      abort(403);
    }
    $review->delete();
    return response()->noContent();
  }
}
