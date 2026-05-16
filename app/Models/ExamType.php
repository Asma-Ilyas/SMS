<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'sort_order', 'is_active'];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}