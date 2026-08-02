<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionField extends Model
{
    protected $fillable = ['section_type_id', 'name', 'field_type', 'options'];
    protected $casts = ['options' => 'array'];
    public function sectionType() { return $this->belongsTo(SectionType::class); }
    public function values() { return $this->hasMany(SectionFieldValue::class, 'field_id'); }
}