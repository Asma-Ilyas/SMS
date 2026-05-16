<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'page_id','section_type_id','sort_order','is_active',
        'position','alignment','style','background_color','text_color'
    ];

    protected $casts = [
        'style'=>'array'
    ];

 
    public function type()
    {
        return $this->belongsTo(SectionType::class, 'section_type_id');
    }

    public function values()
    {
        return $this->hasMany(SectionFieldValue::class);
    }

   public function getStyleStringAttribute()
{
    $style = [];

    if ($this->background_color) {
        $style[] = "background-color:{$this->background_color}";
    }

    if ($this->text_color) {
        $style[] = "color:{$this->text_color}";
    }

    if ($this->alignment) {
        $style[] = "text-align:{$this->alignment}";
    }

    return implode('; ', $style);
}

public function class()
{
    return $this->belongsTo(Classes::class);
}

public function stream()
{
    return $this->belongsTo(Stream::class);
}
}