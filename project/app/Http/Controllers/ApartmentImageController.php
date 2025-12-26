<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentImage;
use App\Http\Resources\ImageResource;
use App\Models\Apartment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ApartmentImageController extends Controller
{
    // index
    public function index(Apartment $apartment){
        $images = $apartment->images;
        return ImageResource::collection($images);
    }

    // show
    public function show($id){
      $image = ApartmentImage::find($id);
      return new ImageResource($image);
    }

    // store
    public function store(Apartment $apartment, Request $request){
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if (auth()->id() !== $apartment->user_id) {
            abort(403, 'Unauthorized');
        }

        $path = $request->file('image')->store('apartments', 'public');

        $image = ApartmentImage::create([
            'apartment_id' => $apartment->id,
            'image_path' => $path,
        ]);

        return new ImageResource($image);
    }
    
    // destroy
    public function destroy(Request $request, $id)
    {
      // check if the owner is the same as the user
      $apartment = Apartment::find($request->apartment_id);
        if (auth()->id() !== $apartment->user_id) {
            abort(403, 'Unauthorized');
        }
        // check if the image exists
        $image = ApartmentImage::find($id);
        if(!$image){
          abort(404, 'Image not found');
        }
        // check if the image is for the apartment
        if($image->apartment->id !== $apartment->id){
            abort(403, 'Unauthorized');
        }

        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }

}
