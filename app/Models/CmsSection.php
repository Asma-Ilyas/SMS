<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsSection extends Model
{
    protected $table = 'cms_sections';
    protected $fillable = ['page_id', 'section_type_id', 'sort_order', 'is_active', 'position', 'alignment', 'style', 'background_color', 'text_color'];
    public function page() { return $this->belongsTo(Page::class); }
    public function sectionType() { return $this->belongsTo(SectionType::class); }
    public function fieldValues() { return $this->hasMany(SectionFieldValue::class); }
}
