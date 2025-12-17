<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    public function store(Request $request) {
    $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'user_id' => 'required|exists:users,id',
        'apartment_id' => 'required|exists:apartments,id',
        'rating' => 'required|integer|min:1|max:5',
        // 'comment' => 'nullable|string'
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
}
