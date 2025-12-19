<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Apartment;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function toggleFavourite(Request $request, $apartmentId)
    {
      $user = User::Find(1); // auth()->user();
      if($user->hasFavorited($apartmentId)){
        $user->favorites()->detach($apartmentId);
        return response()->json(['message' => 'Apartment removed from favorites'], 200);
    }else{
      $user->favorites()->attach($apartmentId);
      return response()->json(['message' => 'Apartment added to favorites'], 200);
    } 
  }
public function index()
{
    $user = User::Find(1); // auth()->user();

    $favorites = DB::table('favorites')
                   ->where('user_id', $user->id)
                   ->select('user_id', 'apartment_id')
                   ->get();

    return response()->json($favorites);
}
}
