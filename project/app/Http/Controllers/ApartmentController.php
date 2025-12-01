<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Http\Resources\ApartmentResource;

class ApartmentController extends Controller
{
    // index
    public function index(){
      $apartments = Apartment::with(['city', 'images'])->paginate(15);
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
        ]);
        
        $validated['user_id'] = auth()->id();
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
      ]);

      if(auth()->id() !== $apartment->owner_id){
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You do not own this apartment.'
        ], 403);
      }

      $apartment->update($validated);
      return new ApartmentResource($apartment);
    }

    // destroy
    public function destroy(Apartment $apartment){
      if(auth()->id() !== $apartment->owner_id){
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You do not own this apartment.'
        ], 403);
      }

      $apartment->delete();
      return response()->noContent();

    }
}
