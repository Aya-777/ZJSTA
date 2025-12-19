<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Booking;

class Review extends Model
{
    protected $guarded = [];
    public function user(){
      return $this->belongsTo(User::class);
    }
    public function booking(){
      return $this->belongsTo(Booking::class);
    }
    public function apartment(){
      return $this->belongsTo(Apartment::class);
    }
}
