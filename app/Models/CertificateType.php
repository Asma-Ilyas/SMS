<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateType extends Model
{
    use HasFactory;

   // Add to $fillable
protected $fillable = ['title', 'description', 'is_active', 'template_file'];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function distributions()
    {
        return $this->hasMany(CertificateDistribution::class);
    }

    public function getTotalDistributedAttribute()
    {
        return $this->distributions()->count();
    }

    // Add accessor for template URL
public function getTemplateUrlAttribute()
{
    return $this->template_file ? asset('storage/' . $this->template_file) : null;
}
}