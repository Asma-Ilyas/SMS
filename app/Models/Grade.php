<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['name', 'numeric_value'];

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }
}
