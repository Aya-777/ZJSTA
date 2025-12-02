<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentImage;
use App\Http\Resources\ImageResource;
use App\Models\Apartment;

class ApartmentImageController extends Controller
{
    // index
    public function index(){
        $images = ApartmentImage::all();
        return ImageResource::collection($images);
    }

    // show
    public function show(Apartment $apartment, ApartmentImage $image){
      if($image->apartment_id != $apartment->id){
          abort(404);
      }
      return new ImageResource($image);
    }

    // create
    public function store(Request $request){
      $validated = $request->validate([
        'apartment_id' => 'required|exists:apartments,id',
        'image_url' => 'required|url',
      ]);
      $image = ApartmentImage::create($validated);
      return new ImageResource($image);
    }

    // update
    public function update(Apartment $apartment, ApartmentImage $image, Request $request){
      $validated = $request->validate([
        'apartment_id' => 'sometimes|exists:apartments,id',
        'image_url' => 'sometimes|url',
      ]);
      $image->update($validated);
      return new ImageResource($image);
    }
    
    // destroy
    public function destroy(Apartment $apartment, ApartmentImage $image){
        if($image->apartment_id != $apartment->id){
            abort(404);
        }
        $image->delete();
        return response()->noContent();
    }

}
