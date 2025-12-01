<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentController;

Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{apartment}', [ApartmentController::class, 'show']);
Route::post('/apartments', [ApartmentController::class, 'store']);
Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']);
Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);

