<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionType extends Model
{
    protected $fillable = ['name', 'slug'];
    public function sections() { return $this->hasMany(CmsSection::class); }
    public function fields() { return $this->hasMany(SectionField::class); }
}