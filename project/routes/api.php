<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentController;

Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{apartment}', [ApartmentController::class, 'show']);
Route::post('/apartments', [ApartmentController::class, 'store']);
Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']);
Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);
Route::get('/apartments/search/{name}',[ApartmentController::class,'search']);

// still needs testing
Route::get('/apartments/{apartment}/images', [ApartmentImageController::class, 'index']);
Route::get('/apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'show']);
Route::post('/apartments/{apartment}/images', [ApartmentImageController::class, 'store']);
Route::put('/apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'update']);
Route::delete('/apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'destroy']);