<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
        protected $fillable = ['room_number', 'capacity', 'type'];
    public function timetableEntries() { return $this->hasMany(TimetableEntry::class); }

}
