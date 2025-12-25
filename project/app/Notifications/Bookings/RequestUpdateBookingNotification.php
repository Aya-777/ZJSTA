<?php

namespace App\Notifications\Bookings;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestUpdateBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $booking;
    public function __construct($booking)
    {
      $this->booking=$booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */

    public function toDatabase($notifiable)
    {
        $message = match($this->booking->status) {
            'confirmed' => "You confirmed the booking for {$this->booking->apartment->title}.",
            'rejected'  => "You rejected the booking for {$this->booking->apartment->title}.",
            'pending'  => "Booking #{$this->booking->id} has been updated by the guest.",
            'cancelled'  => "You cancelled the booking for {$this->booking->apartment->title}.",
            'update_pending' => "A guest has requested to update their booking for {$this->booking->apartment->title}. Please review the changes.",
            default => "The status of your booking for {$this->booking->apartment->title} is {$this->booking->status}.",
        };
      return [
          'booking_id' => $this->booking->id,
          'status' => $this->booking->status,
          'message' => $message,
      ];
    }

}
