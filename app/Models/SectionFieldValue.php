<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionFieldValue extends Model
{
    protected $fillable = ['section_id', 'field_id', 'value'];
    public function section() { return $this->belongsTo(CmsSection::class, 'section_id'); }
    public function field() { return $this->belongsTo(SectionField::class, 'field_id'); }
}
