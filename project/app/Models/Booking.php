<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Payement;
use App\Models\Review;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'pending_modifications' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payement()
    {
        return $this->hasMany(Payement::class);
    }
    public function review()
    {
      return $this->hasOne(Review::class);
    }
}
