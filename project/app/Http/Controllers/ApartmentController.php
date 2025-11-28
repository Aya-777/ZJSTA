<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    // index
    public function index()
    {
      $apartments = Apartment::with(['city', 'images'])->get()->paginate(15);
      return ApartmentResource::collection($apartments);
    }
    // show
    public function show(Apartment $apartment)
    {
      // Eager load related data as before (City and Images)
      $apartment->load(['city', 'images']);
      return new ApartmentResource($apartment);
    }
    // store
    public function store(Request $request){
        $validated = $request->validate([
          'owner_id' => 'required|exists:users,id',
          'city_id' => 'required|exists:cities,id',
          'title' => 'required|string|max:255',
          'description' => 'required|string',
          'price_per_month' => 'required|numeric',
          'rooms' => 'required|integer',
        ]);
        
        $validated['ownerID'] = auth()->id();
        $apartment = Apartment::create($validated);
        return new ApartmentResource($apartment);
        
      }
      // update
      public function update(Request $request, $id){
        $validated = $request->validate([
        'owner_id' => 'required|exists:users,id',
        'city_id' => 'sometimes|exists:cities,id',
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'price_per_month' => 'sometimes|numeric',
        'rooms' => 'sometimes|integer',
      ]);

      $apartment->update($validated);
      return new ApartmentResource($apartment);
    }
    // destroy
    public function destroy($id){

      if(auth()->id() !== $apartment->ownerID){
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You do not own this apartment.'
        ], 403);
      }

      $apartment->delete();
      return response()->noContent();

    }
}
