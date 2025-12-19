<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{

     public function uploadRegistrationImages(Request $request): array
    {
       
        $identityFile = $request->file('identity_image');
        $identityFileName = time() . '_' . $identityFile->hashName();
        $identityImagePath = $identityFile->storeAs('identities', $identityFileName, 'public');

      
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profileFile = $request->file('profile_picture');
            $profileFileName = time() . '_' . $profileFile->hashName();
            $profilePicturePath = $profileFile->storeAs('profiles', $profileFileName, 'public');
        }

        return [
            'identity_image' => $identityImagePath,
            'profile_picture' => $profilePicturePath
        ];
    }
}