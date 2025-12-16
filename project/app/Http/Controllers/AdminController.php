<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index(){
        $users=User::where('role','!=','admin')->orderBy('id','asc')->get();
        return response()->json($users);
    }

    public function approve(User $user){
        $user->is_active=true;
        $user->save();
        return response()->json(['message'=>'User approved successfully.','user'=>$user->fresh()]);
    }

    public function destroy(User $user){
        $user->delete();
        return response()->json(['message'=>'User deleted successfully.'],204);
    }
}
