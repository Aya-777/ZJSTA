<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentImage;
use App\Http\Resources\ImageResource;
use App\Models\Apartment;
use Illuminate\Support\Facades\Storage;

class ApartmentImageController extends Controller
{
    // index
    public function index(Apartment $apartment){
        $images = $apartment->images;
        return ImageResource::collection($images);
    }

    // show
    public function show(Apartment $apartment, ApartmentImage $image){
      return new ImageResource($image);
    }

    // store
    public function store(Apartment $apartment, ApartmentImage $image){
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if (auth()->id() !== $apartment->user_id) {
            abort(403, 'Unauthorized');
        }

        $path = $request->file('image')->store('apartment_images', 'public');

        $image = ApartmentImage::create([
            'apartment_id' => $apartment->id,
            'image_path' => $path,
        ]);

        return new ImageResource($image);
    }
    
    // destroy
    public function destroy(ApartmentImage $image)
    {
        if (auth()->id() !== $image->apartment->user_id) {
            abort(403, 'Unauthorized');
        }

        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }

}
