<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\FcmService;
use App\Notifications\Bookings\NewBookingNotification;
use App\Notifications\Bookings\BookingCompletedNotification;
use App\Notifications\Bookings\UpdateBookingNotification;
use App\Notifications\Bookings\RequestUpdateBookingNotification;

class BookingObserver
{

     protected $fcm;

    public function __construct(FcmService $fcmService)
    {
        $this->fcm = $fcmService;
    }

    public function created(Booking $booking): void
    {
      $apartment = $booking->apartment;
      $owner = $apartment->user;
      $owner->notify(new NewBookingNotification($booking));
      if ($owner->fcm_token) {
          dispatch(function () use ($owner, $booking) {
            try {
              $this->fcm->sendNotification(
                $owner->fcm_token,
                'New Rental Request!',
                'Someone wants to rent your apartment. Please approve or reject.',
                ['booking_id' => (string)$booking->id, 'action' => 'review_request'] 
              );
            } catch (\Exception $e) {
              // Log it, but don't stop the booking from succeeding
              \Log::error("Push failed: " . $e->getMessage());
            }
          })->afterResponse();
      }
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking)
    {
      if ($booking->wasChanged('status') && $booking->status === 'completed') {
          $renter = $booking->user;
          
          // notify renter
          $renter->notify(new BookingCompletedNotification($booking));
          // Send Push to Renter
          if ($renter->fcm_token) {
            dispatch(function () use ($owner, $booking) {
              $this->fcm->sendNotification($renter->fcm_token, 'Booking Completed!', 'Your booking has been completed! Hope you enjoyed your stay. Please rate the apartment.',
              [
                'booking_id' => (string)$booking->id,
                'status' => 'completed'
              ]);
            })->afterResponse();
          }
      }

      if ($booking->wasChanged('status') && ($booking->status === 'confirmed' || $booking->status === 'rejected')) {
        // Notify the renter
        $renter = $booking->user;
        $title = $booking->status === 'confirmed' ? 'Booking Approved!' : 'Booking Rejected!';
        $message = $booking->status === 'confirmed'
          ? "Pack your bags! Your stay at {$booking->apartment->title} is confirmed."
          : "We're sorry to inform you that your booking request for booking {$booking->apartment->title} has been rejected.";

        $this->notifyRenter($renter, $booking, $title, $message);
      }

      if ($booking->wasChanged('status') && $booking->status === 'cancelled') {

        $renter = $booking->user;
        $title = 'Booking Cancelled!';
        $message = "Your booking for {$booking->apartment->title} has been cancelled successfully.";
        $apartment = $booking->apartment;
        $owner = $apartment->user;
        // notify owner
          $owner->notify(new RequestUpdateBookingNotification($booking));
          if ($owner->fcm_token) {
            try {
                $this->fcm->sendNotification(
                    $owner->fcm_token,
                    'Cancelled Booking',
                    'Someone cancelled their booking.',
                    ['booking_id' => (string)$booking->id]
                );
            } catch (\Exception $e) {
                \Log::error("Push failed: " . $e->getMessage());  
            }
          }

        // notify renter
        $this->notifyRenter($renter, $booking, $title, $message);
      }

      if($booking->wasChanged('status') && $booking->status === 'update_pending'){
        $apartment = $booking->apartment;
        $owner = $apartment->user;
        // notify owner
          $owner->notify(new RequestUpdateBookingNotification($booking));
          if ($owner->fcm_token) {
            dispatch(function () use ($owner, $booking) {
              try {
                $this->fcm->sendNotification(
                  $owner->fcm_token,
                  'Update Booking Request',
                  'Someone wants to update their booking. Please approve or reject.',
                  ['booking_id' => (string)$booking->id, 'action' => 'update_request'] 
                );
              } catch (\Exception $e) {
                \Log::error("Push failed: " . $e->getMessage());  
              }
            })->afterResponse();
        }
      }

    }

    protected function notifyRenter($renter, $booking, $title, $message){
      // Save to DB history for Renter
      $renter->notify(new UpdateBookingNotification($booking));

      // Send Push to Renter
      if ($renter->fcm_token) {
        dispatch(function () use ($renter, $booking, $title, $message) {
          $this->fcm->sendNotification($renter->fcm_token, $title, $message, [
            'booking_id' => (string)$booking->id,
            'status' => $booking->status
          ]);
        })->afterResponse();
      }
    }
}
