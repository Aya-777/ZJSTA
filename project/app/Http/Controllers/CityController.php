<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
  // index
    public function index(){
      $cities = City::orderBy('name')->get();
      return CityResource::collection($cities);
    }
    // show
    public function show(City $city){
      return new CityResource($city);
    }
    // i dont think we need the rest but i can do em if u want
}
