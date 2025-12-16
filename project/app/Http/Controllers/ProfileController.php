<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request){
        $user=$request->user();
        return response()->json(['status'=>'success','user'=>$user]);
    }


    public function update(UpdateProfileRequest $request){
          $user =$request->user();
          $user->update($request->validated());

          // $validatedData=$request->validated();
          // if($request->hasFile('profile_picture')){
          //   if($user->profile_picture){
          //     Storage::disk('public')->delete($user->profile_picture);
          //   }
          //   $path=$request->file('profile_picture')->store('profiles','public');
          //   $validatedData['profile_picture']=$path;
          //   $user->update($validatedData);
          // }

          return response()->json([
            'status'=>'success',
            'message'=>'Profile updated successfully',
            'user'=>$user->fresh()
          ]);
    }
}
