<?php
namespace App\Helpers;

class GradeHelper
{
    public static function getGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    public static function getGradePoint($percentage)
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.6;
        if ($percentage >= 70) return 3.2;
        if ($percentage >= 60) return 2.8;
        if ($percentage >= 50) return 2.4;
        if ($percentage >= 40) return 2.0;
        return 0.0;
    }
}