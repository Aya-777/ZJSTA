<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; 
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginRequest;
use App\Traits\FileUploadTrait;

class AuthController extends Controller
{
    use FileUploadTrait;

    public function register(RegisterUserRequest $request){
        // $identityImagePath=$request->file('identity_image')->store('identities','public');
        // $profilePicturePath=null;
        // if($request->hasFile('profile_picture')){
        //     $profilePicturePath=$request->file('profile_picture')->store('profiles','public');
        // }

        $identityImagePath=$this->uploadFile($request,'identity_image','identities');
        $profilePicturePath=$this->uploadFile($request,'profile_picture','profiles');
        if(! $identityImagePath){
            return response()->json(['message'=>'Identity image failed to upload.'],500);
        }
        
        $user=User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'phone_number'=>$request->phone_number,
            'role'=>$request->role,
            // 'profile_picture'=>$request->profile_picture,
            // 'identity_image'=>$request->identity_image,
            'profile_picture'=>$profilePicturePath,
            'identity_image'=>$identityImagePath,
            'birth_date'=>$request->birth_date
        ]);
        return response()->json(['message'=>'Successfully registered.','user'=>$user],201);
    }

    public function login(LoginRequest $request){
        $credintials=$request->validated(); 
        if(!auth()->attempt($credintials)){
            return response()->json(['message'=>'Email or Password is incorect.'],401);
        }
        $user=auth()->user();
        if(! $user->is_active){
            // Auth::logout();? if the account not active logout
            return response()->json(['message'=>'Your account is pending.'],403);
        }
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=>'Login successful','access_token'=>$token,
            'token_type'=>'Bearer','user'=>$user
        ]);
    }

    public function logout(Request $request){
        $user=$request->user();
        $user->currentAccessToken()->delete();
        return response()->json(['message'=>'Successfully logged out']);
    }
}
