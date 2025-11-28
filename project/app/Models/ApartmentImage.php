<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Apartment;

class ApartmentImage extends Model
{
  protected $guarded = [];

  public function apartment()
  {
      return $this->belongsTo(Apartment::class);
  }
}
