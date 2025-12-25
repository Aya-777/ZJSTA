<?php

namespace App\Notifications\Bookings;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateBookingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $booking;
    public function __construct($booking)
    {
        $this->booking = $booking;
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
            'confirmed' => "Your booking for {$this->booking->apartment->title} is confirmed!",
            'rejected'  => "Your booking request was declined.",
            'pending'  => "Your booking request is pending approval.",
            'cancelled' => "Your booking has been cancelled successfully.",
            'update_pending' => "Your booking update request is pending approval.",
            default => "Your booking status is the same.",
        };

      return [
          'booking_id' => $this->booking->id,
          'status' => $this->booking->status,
          'message' => $message,
      ];
    }

}
