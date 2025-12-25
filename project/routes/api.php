<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\ApartmentImageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\OwnerBookingController;

// Apartment Routes
Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{apartment}', [ApartmentController::class, 'show']);
Route::post('/apartments', [ApartmentController::class, 'store']);
Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']);
Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);
Route::get('/apartments/search/{name}',[ApartmentController::class,'search']);

// Apartment Images Routes
Route::get('/apartments/{apartment}/images', [ApartmentImageController::class, 'index']);
Route::get('/apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'show']);
Route::post('/apartments/{apartment}/images', [ApartmentImageController::class, 'store']);
Route::put('/apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'update']);
Route::delete('/apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'destroy']);
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OwnerController;


// Authentication system
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
Route::post('/logout',[AuthController::class,'logout']);
// Profile managment
Route::get('/profile',[ProfileController::class,'show']);
Route::post('/profile/update',[ProfileController::class,'update']);
});

// Admin managment
Route::middleware(['auth:sanctum','is.admin'])->prefix('admin')->group(function(){
Route::get('/users',[AdminController::class,'index']);
Route::post('/users/{user}/approve',[AdminController::class,'approve']);
Route::delete('/users/{user}',[AdminController::class,'destroy']);
});



// Apartment Routes
Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{apartment}', [ApartmentController::class, 'show']);
Route::post('/apartments', [ApartmentController::class, 'store']);
Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']);
Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);
Route::get('/apartments/search/{name}',[ApartmentController::class,'search']);
Route::get('/owner/apartments', [OwnerController::class, 'getMyApartments']);
Route::get('/owner/apartments/{id}/bookings', [OwnerController::class, 'getApartmentBookings']);

// Apartment Images Routes 
Route::get('/apartments/{apartment}/images', [ApartmentImageController::class, 'index']);
Route::get('/apartment-images/{image}', [ApartmentImageController::class, 'show']);
Route::delete('/apartment-images/{image}', [ApartmentImageController::class, 'destroy']);

// Booking Routes
Route::post('/bookings', [BookingController::class, 'store']);

Route::middleware(['auth:sanctum', 'is.owner']) ->group(function () {
  Route::get('/bookings', [BookingController::class, 'index']);
  Route::get('/bookings/{booking}', [BookingController::class, 'show']);
  Route::put('/bookings/{booking}', [BookingController::class, 'update']);
  Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
  Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
  Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
});

// City Routes
Route::apiResource('cities', CityController::class)->only(['index', 'show']);

// upload image
Route::post('/apartments/{apartment}/photos', [PhotoController::class, 'upload']);

// reviews
Route::post('/reviews', [ReviewController::class, 'store']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);
Route::put('/reviews/{review}', [ReviewController::class, 'update']);
Route::delete('/reviews/{review}', [ReviewController::class, 'delete']);

// Favourites Routes
Route::get('/favourites', [FavouriteController::class, 'index']);
Route::post('/favourites/{apartmentId}/toggle', [FavouriteController::class, 'toggleFavourite']);

// Notifications Routes
  Route::get('/notifications', [NotificationController::class, 'index']);
  Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
  Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
  Route::get('/notifications/unread', [NotificationController::class, 'unread']);