<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentUser=Auth::user();
        $booking=$request->route('booking');
        if(! $currentUser || !$booking){
            return response()->json(['message'=>'Unauthorized.'],403);
        }
        if($currentUser->role !== 'owner'){
            return response()->json(['message'=>'Only owners can manage bookings'],403);
        }
        if($currentUser->id !== $booking->apartment->user_id){
            return response()->json(['message'=>'Forbidden:You do not own this apartment for this booking.'],403);
        }
        return $next($request);
    }
}
