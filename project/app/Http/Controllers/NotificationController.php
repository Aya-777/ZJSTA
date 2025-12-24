<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
  public function index() {
    $user = User::find(2); // Temporarily hardcoded for testing use auth()->user();
    return $user->notifications;
  }
  
  public function unread() {
    $user = User::find(2); // Temporarily hardcoded for testing use auth()->user();
    return $user->unreadNotifications;
  }
  
  public function markAsRead($id) {
    $notification = auth()->user()->notifications()->where('id', $id)->first();
    if ($notification) {
      $notification->markAsRead();
      return response()->json(['message' => 'Notification marked as read.']);
    } else {
      return response()->json(['message' => 'Notification not found.'], 404);
    }
  }
  
  public function markAllAsRead()
  {
    $user = User::find(1); // Temporarily hardcoded for testing use auth()->user();
    // auth()->user()->unreadNotifications->markAsRead();
    $user->unreadNotifications->markAsRead();
    return response()->json(['message' => 'All marked as read']);
  }
}
