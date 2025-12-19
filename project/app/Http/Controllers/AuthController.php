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
use App\Http\Controllers\PhotoController;

class AuthController extends Controller
{
    
    public function register(Request $request,PhotoController $photoController){

        $validatedData = $request->validate(
            (new RegisterUserRequest())->rules()
        );

     $imagePaths = $photoController->uploadRegistrationImages($request);

        
        $user=User::create([
            // 'first_name'=>$request->first_name,
            // 'last_name'=>$request->last_name,
            // 'email'=>$request->email,
            // 'password'=>Hash::make($request->password),
            // 'phone_number'=>$request->phone_number,
            // 'role'=>$request->role,
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone_number' => $validatedData['phone_number'],
            'role' => $validatedData['role'],
            'profile_picture'=>$imagePaths['profile_picture'],
            'identity_image'=>$imagePaths['identity_image'],
             'birth_date' => $validatedData['birth_date'],
            // 'birth_date'=>$request->birth_date
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
