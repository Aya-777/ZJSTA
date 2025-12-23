<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
  public function index() {
    return auth()->user()->notifications;
  }

  public function unread() {
    return auth()->user()->unreadNotifications;
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
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All marked as read']);
    }
}
