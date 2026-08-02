<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['school_id', 'title', 'slug'];
    public function school() { return $this->belongsTo(School::class); }
    public function sections() { return $this->hasMany(CmsSection::class); }
}