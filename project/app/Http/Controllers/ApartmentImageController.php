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
