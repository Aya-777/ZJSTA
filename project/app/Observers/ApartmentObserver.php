<?php

namespace App\Observers;

use App\Models\Apartment;
use App\Services\FcmService;
use App\Notifications\Apartments\NewApartmentNotification;
use App\Notifications\Apartments\UpdateApartmentNotification;
use App\Notifications\Apartments\DeleteApartmentNotification;

class ApartmentObserver
{
    protected $fcm;

    public function __construct(FcmService $fcmService)
    {
        $this->fcm = $fcmService;
    }
    /**
     * Handle the Apartment "created" event.
     */
    public function created(Apartment $apartment): void
    {
        $owner = $apartment->user;
        $owner->notify(new NewApartmentNotification($apartment));
        if ($owner->fcm_token) {
          try {
              $this->fcm->sendNotification(
                  $owner->fcm_token,
                  'Apartment Live!',
                  "Your apartment '{$apartment->title}' is now listed on zjsta.",
                  ['apartment_id' => (string)$apartment->id]
              );
          } catch (\Exception $e) {
              // Log it, but don't stop the booking from succeeding
              \Log::error("Push failed: " . $e->getMessage());
          }
        }
    }

    /**
     * Handle the Apartment "updated" event.
     */
    public function updated(Apartment $apartment): void
    {
        $owner = $apartment->user;
        $owner->notify(new UpdateApartmentNotification($apartment));
        if ($owner->fcm_token) {
          try {
              $this->fcm->sendNotification(
                  $owner->fcm_token,
                  'You updated your apartment.',
                  'Your apartment has been updated successfully.',
                  ['apartment_id' => (string)$apartment->id] 
              );
          } catch (\Exception $e) {
              // Log it, but don't stop the booking from succeeding
              \Log::error("Push failed: " . $e->getMessage());
          }
        }
    }

    /**
     * Handle the Apartment "deleted" event.
     */
    public function deleted(Apartment $apartment): void
    {
        $owner = $apartment->user;
        $owner->notify(new DeleteApartmentNotification($apartment));
        if ($owner->fcm_token) {
          try {
              $this->fcm->sendNotification(
                  $owner->fcm_token,
                  'You deleted your apartment.',
                  'Your apartment has been deleted successfully.',
                  ['apartment_id' => (string)$apartment->id] 
              );
          } catch (\Exception $e) {
              // Log it, but don't stop the booking from succeeding
              \Log::error("Push failed: " . $e->getMessage());
          }
        }
    }
}
