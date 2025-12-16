<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{

    public function upload(Request $request, Apartment $apartment)
    {
       $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Note: The storeAs method returns the path relative to the disk root ('images/...')
            $path = $file->storeAs(
                'images/apartments/' . $apartment->id,
                time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            $apartmentImage = $apartment->images()->create([
                'image_path' => $path, 
            ]);

            $url = asset(Storage::url($path));

            return response()->json([
                'message' => 'Photo uploaded and recorded successfully!',
                'image_id' => $apartmentImage->id,
                'url' => $url,
            ], 201);
        }

        return response()->json(['error' => 'No photo provided.'], 400);
    }
}