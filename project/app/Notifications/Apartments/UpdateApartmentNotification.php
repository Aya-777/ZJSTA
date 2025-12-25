<?php

namespace App\Notifications\Apartments;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateApartmentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $apartment;
    public function __construct($apartment)
    {
        $this->apartment = $apartment;
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
    public function toDatabase(object $notifiable): array
    {
      return [
        'apartment_id' => $this->apartment->id,
        'title' => 'Apartment Updated',
        'message' => 'You have successfully updated your apartment: ' . $this->apartment->title,
        ];
    }
}
