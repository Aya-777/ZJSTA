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


// Booking Routes
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/bookings/{booking}', [BookingController::class, 'show']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::put('/bookings/{booking}', [BookingController::class, 'update']);
Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

// City Routes
Route::apiResource('cities', CityController::class)->only(['index', 'show']);

// upload image
Route::post('/apartments/{apartment}/photos', [PhotoController::class, 'upload']);

// Owner's Approval/Rejection Routes 
Route::middleware(['auth:sanctum', 'is.owner']) ->group(function () {
    Route::post('/owner/bookings/{booking}/approve', [OwnerBookingController::class, 'approve']);
    Route::post('/owner/bookings/{booking}/reject', [OwnerBookingController::class, 'reject']);
});