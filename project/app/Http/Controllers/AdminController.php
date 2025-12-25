<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApprovedEmail;

class AdminController extends Controller
{
    public function index(){
        $users=User::where('role','!=','admin')->orderBy('id','asc')->get();
        return response()->json($users);
    }

    public function approve(User $user){
        $user->is_active=true;
        $user->save();
        Mail::to($user->email)->send(new AccountApprovedEmail($user));
        return response()->json(['message'=>'User approved successfully.','user'=>$user->fresh()]);
    }

    public function destroy(User $user){
        $user->delete();
        return response()->json(['message'=>'User deleted successfully.'],204);
    }
}
