<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $fillable = ['name', 'code', 'level', 'block_id'];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}