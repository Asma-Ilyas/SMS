<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionFieldValue extends Model
{
    protected $fillable = ['section_id','field_id','value'];

    public function field()
    {
        return $this->belongsTo(SectionField::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
