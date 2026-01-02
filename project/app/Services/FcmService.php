<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;

class FcmService
{
    protected $messaging;

    protected $userRepository;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->messaging = $firebase->createMessaging();
    }

    public function sendNotification($deviceToken, $title, $body, array $data = [])
    {
      $notification = Notification::create($title, $body);

      $message = CloudMessage::withTarget('token', $deviceToken)
          ->withNotification($notification)
          ->withData($data) 
          ->withAndroidConfig(
              AndroidConfig::fromArray([
                  'priority' => 'high', // Ensures faster delivery
                  'notification' => [
                      'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                  ],
              ])
            );

      try {
          return $this->messaging->send($message);
      } catch (\Kreait\Firebase\Exception\MessagingException $e) {
          // Handle invalid tokens
          \Log::error('FCM Error: ' . $e->getMessage());
          throw $e;
      } catch (\Exception $e) {
          \Log::error('General Error sending FCM: ' . $e->getMessage());
          throw $e;
      }
    }
}
