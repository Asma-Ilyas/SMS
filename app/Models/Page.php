<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'slug',
    ];

    /**
     * A page belongs to a school.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * A page has many sections.
     */
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }
}