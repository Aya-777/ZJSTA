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
      dd($request->all());

        // 1. Validate the request
       $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // 2. Generate unique filename and store the file
            // Note: The storeAs method returns the path relative to the disk root ('images/...')
            $path = $file->storeAs(
                'images/apartments/' . $apartment->id,
                time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            // 3. Create the Database Record (Crucial Step!)
            $apartmentImage = $apartment->images()->create([
                // Assuming you renamed the column to 'image_path'
                'image_path' => $path, 
            ]);

            // 4. Generate the public URL
            $url = asset(Storage::url($path));

            // 5. Return success response
            return response()->json([
                'message' => 'Photo uploaded and recorded successfully!',
                'image_id' => $apartmentImage->id,
                'url' => $url,
            ], 201);
        }

        return response()->json(['error' => 'No photo provided.'], 400);
    }
}