<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamMark;
use App\Models\ClassSection;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ExamType;
use App\Models\ExamGroup;
use App\Models\GradeScale;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamReportController extends Controller
{
    /**
     * Exam report dashboard
     */
    public function index(Request $request)
    {
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        $examTypes = ExamType::where('is_active', true)->get();
        $examGroups = ExamGroup::where('is_active', true)->get();

        $reports = $this->getExamReports($request);

        return view('admin.exams.reports.index', compact(
            'classSections', 'examTypes', 'examGroups', 'reports'
        ));
    }

    /**
     * Show class-wise report form
     */
    public function classWiseForm()
    {
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        $exams = Exam::with(['examType', 'classSection'])->where('is_published', true)->get();
        
        return view('admin.exams.reports.class-wise-form', compact('classSections', 'exams'));
    }

    /**
     * Class-wise exam report
     */
    public function classWiseReport(Request $request)
    {
        $classSectionId = $request->class_section_id;
        $examId = $request->exam_id;

        if (!$classSectionId || !$examId) {
            return redirect()->route('admin.exams.reports.class-wise-form')
                ->with('error', 'Please select both class and exam.');
        }

        $classSection = ClassSection::with(['class.grade', 'class.stream'])->findOrFail($classSectionId);
        $exam = Exam::with(['examType', 'examGroup'])->findOrFail($examId);

        $students = Student::where('class_section_id', $classSectionId)
            ->orderBy('first_name')
            ->get();

        $subjects = Subject::whereHas('subjectAssignments', function($q) use ($classSectionId) {
            $q->where('class_section_id', $classSectionId);
        })->get();

        $results = ExamResult::where('exam_id', $examId)
            ->where('class_section_id', $classSectionId)
            ->get()
            ->keyBy('student_id');

        $marks = DB::table('exam_marks')
            ->where('exam_id', $examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $gradeScale = GradeScale::getDefault();

        $subjectStats = [];
        foreach ($subjects as $subject) {
            $subjectMarks = DB::table('exam_marks')
                ->where('exam_id', $examId)
                ->where('subject_id', $subject->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
            
            $count = $subjectMarks->count();
            $total = $subjectMarks->sum('marks_obtained');
            $avg = $count > 0 ? round($total / $count, 2) : 0;
            $max = $subjectMarks->max('marks_obtained') ?? 0;
            $min = $subjectMarks->min('marks_obtained') ?? 0;

            $subjectStats[] = [
                'subject' => $subject,
                'count' => $count,
                'total' => $total,
                'average' => $avg,
                'max' => $max,
                'min' => $min,
            ];
        }

        return view('admin.exams.reports.class-wise', compact(
            'classSection', 'exam', 'students', 'subjects', 'results', 'marks', 
            'gradeScale', 'subjectStats'
        ));
    }

    /**
     * Show student-wise report form
     */
    public function studentWiseForm()
    {
        $classes = \App\Models\Classes::with(['grade', 'stream'])->get();
        $students = collect();
        
        if (request('class_id')) {
            $classSectionIds = ClassSection::where('class_id', request('class_id'))
                ->pluck('id')
                ->toArray();
            $students = Student::whereIn('class_section_id', $classSectionIds)
                ->where('status', 'Active')
                ->orderBy('first_name')
                ->get();
        }
        
        if (request('class_section_id')) {
            $students = Student::where('class_section_id', request('class_section_id'))
                ->where('status', 'Active')
                ->orderBy('first_name')
                ->get();
        }
        
        $sections = collect();
        if (request('class_id')) {
            $sections = ClassSection::where('class_id', request('class_id'))
                ->with(['class.grade', 'class.stream'])
                ->get();
        }
        
        return view('admin.exams.reports.student-wise-form', compact('classes', 'students', 'sections'));
    }

    /**
     * Student-wise exam report - COMPLETE FIXED VERSION
     */
    public function studentWiseReport(Request $request)
    {
        $studentId = $request->student_id;

        if (!$studentId) {
            return redirect()->route('admin.exams.reports.student-wise-form')
                ->with('error', 'Please select a student.');
        }

        // 1. Get student with relationships
        $student = Student::with([
            'classSection', 
            'classSection.class', 
            'classSection.class.grade', 
            'classSection.class.stream'
        ])->findOrFail($studentId);
        
        // 2. Get all published exams for this student's class
        $exams = Exam::where('class_section_id', $student->class_section_id)
            ->where('is_published', true)
            ->with(['examType', 'examGroup'])
            ->orderBy('start_date', 'desc')
            ->get();

        // 3. Get exam results
        $results = ExamResult::where('student_id', $studentId)
            ->get()
            ->keyBy('exam_id');

        // 4. Get subject-wise marks for each exam (WITH SUBJECT NAMES)
        $examMarks = [];
        $allSubjectMarks = collect();
        
        foreach ($exams as $exam) {
            $marks = DB::table('exam_marks')
                ->where('exam_id', $exam->id)
                ->where('student_id', $studentId)
                ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
                ->select(
                    'exam_marks.*',
                    'subjects.name as subject_name',
                    'subjects.code as subject_code'
                )
                ->get();
            
            $examMarks[$exam->id] = $marks;
            $allSubjectMarks = $allSubjectMarks->merge($marks);
        }

        // 5. Get subject-wise summary
        $subjectResults = collect();
        if ($allSubjectMarks->count() > 0) {
            $subjectResults = $allSubjectMarks->groupBy('subject_id')->map(function($marks, $subjectId) {
                $subjectName = $marks->first()->subject_name ?? 'N/A';
                $subjectCode = $marks->first()->subject_code ?? '';
                $examCount = $marks->count();
                $avgMarks = $marks->avg('marks_obtained') ?? 0;
                $avgMaxMarks = $marks->avg('max_marks') ?? 100;
                $avgPercentage = $avgMaxMarks > 0 ? ($avgMarks / $avgMaxMarks) * 100 : 0;
                $passedCount = $marks->filter(function($m) {
                    return $m->marks_obtained >= $m->passing_marks;
                })->count();
                $failedCount = $marks->filter(function($m) {
                    return $m->marks_obtained < $m->passing_marks;
                })->count();
                $highest = $marks->max('marks_obtained') ?? 0;
                $lowest = $marks->min('marks_obtained') ?? 0;
                
                return (object) [
                    'subject_id' => $subjectId,
                    'subject_name' => $subjectName,
                    'subject_code' => $subjectCode,
                    'exam_count' => $examCount,
                    'average_marks' => $avgMarks,
                    'average_max_marks' => $avgMaxMarks,
                    'average_percentage' => $avgPercentage,
                    'passed_count' => $passedCount,
                    'failed_count' => $failedCount,
                    'highest_marks' => $highest,
                    'lowest_marks' => $lowest,
                ];
            })->values();
        }

        // 6. Get attendance data
        $attendanceData = $this->getStudentAttendance($studentId);

        return view('admin.exams.reports.student-wise', compact(
            'student', 
            'exams', 
            'results', 
            'examMarks', 
            'subjectResults',
            'attendanceData'
        ));
    }

    /**
     * Get student attendance
     */
    private function getStudentAttendance($studentId, $dateFrom = null, $dateTo = null)
    {
        $query = StudentAttendance::where('student_id', $studentId);
        
        if ($dateFrom) {
            $query->where('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('date', '<=', $dateTo);
        }
        
        $attendance = $query->orderBy('date', 'desc')->get();
        
        if ($attendance->isEmpty()) {
            return [
                'has_data' => false,
                'total_days' => 0,
                'present_days' => 0,
                'absent_days' => 0,
                'late_days' => 0,
                'half_days' => 0,
                'leave_days' => 0,
                'percentage' => 0,
                'monthly' => [],
                'daily' => [],
            ];
        }
        
        $totalDays = $attendance->count();
        $presentDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $lateDays = $attendance->where('status', 'late')->count();
        $halfDays = $attendance->where('status', 'half_day')->count();
        $leaveDays = $attendance->where('status', 'leave')->count();
        
        $attended = $presentDays + $lateDays + $halfDays;
        $percentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 2) : 0;
        
        // Monthly breakdown
        $monthlyData = [];
        foreach ($attendance->groupBy(function($item) {
            return $item->date->format('Y-m');
        }) as $month => $records) {
            $monthTotal = $records->count();
            $monthPresent = $records->where('status', 'present')->count();
            $monthAbsent = $records->where('status', 'absent')->count();
            $monthLate = $records->where('status', 'late')->count();
            $monthHalf = $records->where('status', 'half_day')->count();
            $monthAttended = $monthPresent + $monthLate + $monthHalf;
            $monthPercentage = $monthTotal > 0 ? round(($monthAttended / $monthTotal) * 100, 2) : 0;
            
            $monthlyData[Carbon::parse($month . '-01')->format('F Y')] = [
                'total' => $monthTotal,
                'present' => $monthPresent,
                'absent' => $monthAbsent,
                'late' => $monthLate,
                'half' => $monthHalf,
                'percentage' => $monthPercentage,
            ];
        }
        
        // Daily breakdown (last 30 days)
        $dailyData = [];
        foreach ($attendance->take(30) as $att) {
            $dailyData[$att->date->format('Y-m-d')] = [
                'date' => $att->date->format('d M Y'),
                'day' => $att->date->format('l'),
                'status' => $att->status,
            ];
        }
        
        return [
            'has_data' => true,
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'half_days' => $halfDays,
            'leave_days' => $leaveDays,
            'percentage' => $percentage,
            'monthly' => $monthlyData,
            'daily' => $dailyData,
            'date_range_display' => 'All Time',
        ];
    }

    /**
     * Show subject-wise report form
     */
    public function subjectWiseForm()
    {
        $subjects = Subject::where('is_active', true)->get();
        $classes = \App\Models\Classes::with(['grade', 'stream'])->get();
        
        $sections = collect();
        if (request('class_id')) {
            $sections = ClassSection::where('class_id', request('class_id'))
                ->with(['class.grade', 'class.stream'])
                ->get();
        }
        
        return view('admin.exams.reports.subject-wise-form', compact('subjects', 'classes', 'sections'));
    }

    /**
     * Subject-wise exam report
     */
    public function subjectWiseReport(Request $request)
    {
        $subjectId = $request->subject_id;

        if (!$subjectId) {
            return redirect()->route('admin.exams.reports.subject-wise-form')
                ->with('error', 'Please select a subject.');
        }

        $subject = Subject::findOrFail($subjectId);
        
        $examResults = ExamMark::select('exam_marks.*')
            ->where('exam_marks.subject_id', $subjectId)
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exams.is_published', true)
            ->orderBy('exams.start_date', 'desc')
            ->with([
                'exam', 
                'student', 
                'student.classSection', 
                'student.classSection.class.grade', 
                'student.classSection.class.stream'
            ])
            ->paginate(20);

        return view('admin.exams.reports.subject-wise', compact('subject', 'examResults'));
    }

    /**
     * Performance analysis report
     */
    public function performanceAnalysis(Request $request)
    {
        $classSectionId = $request->class_section_id;
        $examTypeId = $request->exam_type_id;
        $fromDate = $request->from_date ?? now()->subMonths(6)->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();

        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        $examTypes = ExamType::where('is_active', true)->get();

        $stats = [];

        if ($classSectionId) {
            $students = Student::where('class_section_id', $classSectionId)->get();
            
            foreach ($students as $student) {
                $query = ExamResult::where('student_id', $student->id)
                    ->whereBetween('created_at', [$fromDate, $toDate]);

                if ($examTypeId) {
                    $query->whereHas('exam', function($q) use ($examTypeId) {
                        $q->where('exam_type_id', $examTypeId);
                    });
                }

                $results = $query->get();
                
                $stats[] = [
                    'student' => $student,
                    'total_exams' => $results->count(),
                    'average_percentage' => $results->avg('percentage') ?? 0,
                    'best_percentage' => $results->max('percentage') ?? 0,
                    'worst_percentage' => $results->min('percentage') ?? 0,
                    'pass_count' => $results->where('remarks', 'Pass')->count(),
                    'fail_count' => $results->where('remarks', 'Fail')->count(),
                ];
            }

            usort($stats, function($a, $b) {
                return $b['average_percentage'] <=> $a['average_percentage'];
            });
        }

        return view('admin.exams.reports.performance-analysis', compact(
            'classSections', 'examTypes', 'stats', 'fromDate', 'toDate'
        ));
    }

    /**
     * Grade distribution report
     */
    public function gradeDistribution(Request $request)
    {
        $examId = $request->exam_id;
        $classSectionId = $request->class_section_id;

        $exams = Exam::where('is_published', true)
            ->with(['examType', 'classSection'])
            ->orderBy('start_date', 'desc')
            ->get();
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();

        if (!$examId) {
            return view('admin.exams.reports.grade-distribution-form', compact('exams', 'classSections'));
        }

        $exam = Exam::findOrFail($examId);
        $gradeScale = GradeScale::getDefault();
        $gradeScaleData = $gradeScale ? $gradeScale->grades : [];

        $query = ExamResult::where('exam_id', $examId);
        if ($classSectionId) {
            $query->where('class_section_id', $classSectionId);
        }
        $results = $query->get();

        $distribution = [];
        foreach ($gradeScaleData as $range) {
            $distribution[$range['grade']] = [
                'grade' => $range['grade'],
                'min' => $range['min'],
                'max' => $range['max'],
                'count' => 0,
                'students' => [],
            ];
        }

        foreach ($results as $result) {
            foreach ($gradeScaleData as $range) {
                if ($result->percentage >= $range['min'] && $result->percentage <= $range['max']) {
                    if (isset($distribution[$range['grade']])) {
                        $distribution[$range['grade']]['count']++;
                        $distribution[$range['grade']]['students'][] = $result->student_id;
                    }
                    break;
                }
            }
        }

        $totalStudents = $results->count();

        return view('admin.exams.reports.grade-distribution', compact(
            'exam', 'gradeScaleData', 'distribution', 'totalStudents', 
            'classSectionId', 'exams', 'classSections'
        ));
    }

    /**
     * Grade distribution form
     */
    public function gradeDistributionForm()
    {
        $exams = Exam::where('is_published', true)
            ->with(['examType', 'classSection'])
            ->orderBy('start_date', 'desc')
            ->get();
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        
        return view('admin.exams.reports.grade-distribution-form', compact('exams', 'classSections'));
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $type = $request->type;
        $examId = $request->exam_id;
        $classSectionId = $request->class_section_id;

        if ($type == 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="exam-report.csv"',
            ];

            $callback = function() use ($examId, $classSectionId) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Student', 'Total Marks', 'Percentage', 'Grade', 'Rank']);

                $query = ExamResult::with('student')->where('exam_id', $examId);
                if ($classSectionId) {
                    $query->where('class_section_id', $classSectionId);
                }
                $results = $query->get();

                foreach ($results as $result) {
                    fputcsv($handle, [
                        $result->student->first_name . ' ' . $result->student->last_name,
                        $result->total_marks,
                        $result->percentage . '%',
                        $result->grade,
                        '#' . $result->rank_in_class
                    ]);
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json(['message' => 'Export format not supported']);
    }

    /**
     * Get exam reports
     */
    private function getExamReports($request)
    {
        return Exam::with(['examType', 'classSection'])
            ->where('is_published', true)
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Calculate grade based on percentage
     */
    public function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
}