<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    protected $fillable = ['name'];
    public function electiveTracks() { return $this->hasMany(ElectiveTrack::class); }
    public function classes() { return $this->hasMany(Classes::class); }
}
