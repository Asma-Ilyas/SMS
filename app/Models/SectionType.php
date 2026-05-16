<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * A section type can have many sections.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * A section type can have many fields.
     */
    public function fields()
    {
        return $this->hasMany(SectionField::class);
    }
}