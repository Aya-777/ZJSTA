<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
  public function index() {
    $user = auth()->user(); 
    return $user->notifications;
  }
  
  public function unread() {
    $user = auth()->user(); 
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
    $user = auth()->user();
    $user->unreadNotifications->markAsRead();
    return response()->json(['message' => 'All marked as read']);
  }
}
