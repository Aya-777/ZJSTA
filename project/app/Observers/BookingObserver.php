<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\FcmService;
use App\Notifications\NewBookingNotification;
use App\Notifications\BookingCompletedNotification;
use App\Notifications\UpdateBookingNotification;
use App\Notifications\RequestUpdateBookingNotification;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
      $apartment = $booking->apartment;
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
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking)
    {
      // Only trigger if the status was changed to 'completed'
      if ($booking->wasChanged('status') && $booking->status === 'completed') {
          $renter = $booking->user;
          
          // notify renter
          $renter->notify(new BookingCompletedNotification($booking));
          // Send Push to Renter
          if ($renter->fcm_token) {
            $fcm->sendNotification($renter->fcm_token, 'Booking Completed!', 'Your booking has been completed! HOpe you enjoyed your stay. Please rate the apartment.',
             [
                'booking_id' => (string)$booking->id,
                'status' => 'completed'
            ]);
          }
      }

      // Only trigger if the status was changed to 'confirmed/rejected'
      if ($booking->wasChanged('status') && ($booking->status === 'confirmed' || $booking->status === 'rejected')) {
        // Notify the renter
        $renter = $booking->user;
        $title = $booking->status === 'confirmed' ? 'Booking Approved!' : 'Booking Rejected!';
        $message = $booking->status === 'confirmed'
          ? "Pack your bags! Your stay at {$booking->apartment->title} is confirmed."
          : "We're sorry to inform you that your booking request for booking {$booking->apartment->title} has been rejected.";

        $this->notifyRenter($renter, $booking, $title, $message);
      }

      if($booking->wasChanged('status') && $booking->status === 'update_pending'){
        $apartment = $booking->apartment;
        $owner = $apartment->user;
        // notify owner
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
      }

    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
      
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
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
