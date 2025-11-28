<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Apartment;

class City extends Model
{
  protected $guarded = [];

  public function apartments()
  {
      return $this->hasMany(Apartment::class);
  }
}
