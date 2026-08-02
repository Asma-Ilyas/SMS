<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['name', 'slug', 'logo'];
    public function pages() { return $this->hasMany(Page::class); }
}