<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerBookingController extends Controller
{
      public function approve(Booking $booking) {
        $booking->status = 'approved';
        $booking->save();
        return response()->json(['message' => 'Booking approved.', 'booking' => new BookingResource($booking)]);
    }
    public function reject(Booking $booking) {
        $booking->status = 'rejected';
        $booking->save();
        return response()->json(['message' => 'Booking rejected.', 'booking' => new BookingResource($booking)]);
    }
}
