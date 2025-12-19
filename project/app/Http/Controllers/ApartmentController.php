<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Http\Resources\ApartmentResource;
use App\Http\Filters\ApartmentFilter;
use App\Http\Controllers\ApartmentImageController;
use App\Models\ApartmentImage;

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
          'numberOfBathrooms' => 'required|integer',
          'is_furnished' => 'required|boolean',
          'rental_type' => 'sometimes|string',
          'area' => 'required|numeric',
          'property_type' => 'required|string',
        ]);
        $request->validate([
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        $apartment = Apartment::create($validated);

        if ($request->hasFile('images')) {
          $images = $request->file('images');

          if (!is_array($images)) {
              $images = [$images];
          }


          foreach ($images as $image) {
              $path = $image->store('apartments', 'public');

              ApartmentImage::create([
                  'apartment_id' => $apartment->id,
                  'image_path' => $path,
              ]);
          }
        }
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
        'numberOfBathrooms' => 'sometimes|integer',
        'is_furnished' => 'sometimes|boolean',
        'rental_type' => 'sometimes|string',
        'area' => 'sometimes|numeric',
        'property_type' => 'sometimes|string',
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
