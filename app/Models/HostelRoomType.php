<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelRoomType extends Model
{
    protected $fillable = ['name', 'default_capacity', 'description'];

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class, 'room_type_id');
    }
}
