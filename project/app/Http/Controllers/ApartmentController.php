<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Http\Resources\ApartmentResource;
use App\Http\Filters\ApartmentFilter;

class ApartmentController extends Controller
{
    // index
    public function index(Request $request){
      $query = Apartment::query();
      $query = ApartmentFilter::apply($query ,$request);
      $apartments = $query->with(['city', 'images'])->paginate(15);
      return ApartmentResource::collection($apartments);
    }

    // show
    public function show(Apartment $apartment){
      $apartment->load(['city', 'images']);
      return new ApartmentResource($apartment);
    }

    // store
    public function store(Request $request){
        $validated = $request->validate([
          'user_id' => 'required',
          'city_id' => 'required',
          'title' => 'required|string|max:255',
          'description' => 'required|string',
          'pricePerMonth' => 'required|numeric',
          'numberOfRooms' => 'required|integer',
          'furnished' => 'required|boolean',
          'rentalType' => 'required|string',
          'area' => 'required|numeric',
          ]);
        
        $apartment = Apartment::create($validated);
        return new ApartmentResource($apartment);
        
      }

      // update
      public function update(Request $request , Apartment $apartment){
        $validated = $request->validate([
        'user_id' => 'sometimes|exists:users,id',
        'city_id' => 'sometimes|exists:cities,id',
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'pricePerMonth' => 'sometimes|numeric',
        'numberOfRooms' => 'sometimes|integer',
          'furnished' => 'sometimes|boolean',
          'rentalType' => 'sometimes|string',
          'area' => 'sometimes|numeric',
      ]);

      $apartment->update($validated);
      return new ApartmentResource($apartment);
    }

    // destroy
    public function destroy(Apartment $apartment){

      $apartment->delete();
      return response()->noContent();

    }

    //search
    public function search($name){
      $apartments = Apartment::where('title', 'like', '%' . $name . '%')->with(['city', 'images'])->get();
      return ApartmentResource::collection($apartments);
    }
}
