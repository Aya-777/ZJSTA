<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentImage;
use App\Http\Resources\ImageResource;

class ApartmentImageController extends Controller
{
    // index
    public function index(){
        $images = ApartmentImage::all();
        return ImageResource::collection($images);
    }

    // show
    public function show($image){
      return new ImageResource($image);
    }

    // create
    public function store(Request $request, ApartmentImage $image){
      $validated = $request->validate([
        'apartment_id' => 'required|exists:apartments,id',
        'image_url' => 'required|url',
      ]);
      $image = ApartmentImage::create($validated);
      return new ImageResource($image);
    }

    // update
    public function update(Request $request, ApartmentImage $image){
      $validated = $request->validate([
        'apartment_id' => 'required|exists:apartments,id',
        'image_url' => 'required|url',
      ]);
      $image->update($validated);
      return new ImageResource($image);
    }
    
    // destroy
    public function destroy(ApartmentImage $image){
        $image->delete();
        return response()->noContent();
    }

}
