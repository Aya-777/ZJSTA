<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Apartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function toggleFavourite(Request $request, $apartmentId)
    {
      $user = auth()->user();
      if($user->id != $request->user_id){
        abort(403);
      }
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
    $user = auth()->user();

    $favorites = DB::table('favorites')
                   ->where('user_id', $user->id)
                   ->select('user_id', 'apartment_id')
                   ->get();

    return response()->json($favorites);
}
}
