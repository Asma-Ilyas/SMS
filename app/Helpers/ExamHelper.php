<?php

namespace App\Helpers;

use App\Models\GradeScale;
use App\Models\ExamResult;
use App\Models\ExamMark;
use App\Models\Student;

class ExamHelper
{
    public static function getGrade($percentage)
    {
        $scale = GradeScale::where('is_default', true)->first();
        if (!$scale) return 'N/A';
        foreach ($scale->grades as $grade) {
            if ($percentage >= $grade['min'] && $percentage <= $grade['max']) {
                return $grade['grade'];
            }
        }
        return 'F';
    }

    public static function calculateAndStoreResult($examId, $studentId)
    {
        $marks = ExamMark::where('exam_id', $examId)
                    ->where('student_id', $studentId)
                    ->get();

        $totalMarks = $marks->sum('marks_obtained');
        $totalMax = $marks->sum('max_marks');
        $percentage = $totalMax > 0 ? round(($totalMarks / $totalMax) * 100, 2) : 0;
        $grade = self::getGrade($percentage);
        $remarks = $percentage >= 40 ? 'Pass' : 'Fail'; // passing criteria

        $student = Student::find($studentId);
        $classId = $student->class_id;
        $sectionId = $student->section_id ?? null;

        return ExamResult::updateOrCreate(
            ['exam_id' => $examId, 'student_id' => $studentId],
            [
                'class_id' => $classId,
                'section_id' => $sectionId,
                'total_marks' => $totalMarks,
                'total_max_marks' => $totalMax,
                'percentage' => $percentage,
                'grade' => $grade,
                'remarks' => $remarks,
            ]
        );
    }

    public static function recalculateRanks($examId)
    {
        $results = ExamResult::where('exam_id', $examId)
                    ->orderBy('percentage', 'desc')
                    ->get();

        $rank = 1;
        $prevPercentage = null;
        foreach ($results as $index => $result) {
            if ($prevPercentage !== null && $result->percentage < $prevPercentage) {
                $rank = $index + 1;
            }
            $result->rank_in_class = $rank;
            $result->save();
            $prevPercentage = $result->percentage;
        }
    }
}