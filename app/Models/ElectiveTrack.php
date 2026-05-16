<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectiveTrack extends Model
{
     protected $fillable = ['stream_id', 'name'];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }
}
