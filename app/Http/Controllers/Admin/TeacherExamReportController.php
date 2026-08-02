<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Subject;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // ADD THIS LINE
use Carbon\Carbon;

class TeacherExamReportController extends Controller
{
    /**
     * Display teacher-wise exam results with subject filtering
     */
    public function index(Request $request)
    {
        // Get all teachers
        $teachers = Staff::where('is_teacher', true)
                        ->orderBy('first_name')
                        ->get();

        // Get all exams for filter
        $exams = Exam::with(['examType', 'classSection'])
                    ->where('is_published', true)
                    ->orderBy('start_date', 'desc')
                    ->get();

        // Initialize variables
        $selectedTeacher = null;
        $selectedSubject = null;
        $availableSubjects = collect();
        $availableExams = collect();
        $subjectResults = [];
        $subjectTotals = [];
        $teacherSummary = null;
        $studentsList = [];
        $passedStudents = [];
        $failedStudents = [];
        $debugInfo = [];

        // Get teacher ID from request
        $teacherId = $request->input('teacher_id');
        
        // If teacher is selected, get their subjects and available exams
        if ($teacherId) {
            $selectedTeacher = Staff::find($teacherId);
            
            if ($selectedTeacher) {
                // Get subjects taught by this teacher
                $teacherSubjects = TeacherSubject::where('teacher_id', $selectedTeacher->id)
                                                ->with('subject')
                                                ->get();
                
                $debugInfo['teacher_subjects_count'] = $teacherSubjects->count();
                
                $availableSubjects = collect();
                foreach ($teacherSubjects as $ts) {
                    if ($ts->subject) {
                        $availableSubjects->push($ts->subject);
                    }
                }
                
                $debugInfo['available_subjects'] = $availableSubjects->pluck('name')->toArray();
                
                $teacherSubjectIds = $teacherSubjects->pluck('subject_id')->toArray();
                
                if (!empty($teacherSubjectIds)) {
                    // Get exams that have marks for this teacher's subjects
                    $examIds = DB::table('exam_marks')
                        ->whereIn('subject_id', $teacherSubjectIds)
                        ->distinct()
                        ->pluck('exam_id')
                        ->toArray();
                    
                    $debugInfo['exam_ids_with_marks'] = $examIds;
                    
                    $availableExams = Exam::where('is_published', true)
                        ->whereIn('id', $examIds)
                        ->with('classSection')
                        ->orderBy('start_date', 'desc')
                        ->get();
                    
                    $debugInfo['available_exams_count'] = $availableExams->count();
                } else {
                    $debugInfo['no_subject_ids'] = true;
                }
            }
        }

        // If subject is selected, get detailed results for that subject
        $subjectId = $request->input('subject_id');
        if ($subjectId && $selectedTeacher) {
            $selectedSubject = Subject::find($subjectId);
            
            if ($selectedSubject) {
                $subjectResults = $this->getSubjectWiseResults(
                    $selectedTeacher->id, 
                    $selectedSubject->id, 
                    $request->input('exam_id')
                );
                
                $debugInfo['subject_results_count'] = count($subjectResults);
                $debugInfo['subject_id'] = $subjectId;
                $debugInfo['teacher_id'] = $selectedTeacher->id;
                
                $subjectTotals = $this->calculateSubjectTotals($subjectResults);
                $teacherSummary = $this->getTeacherSummary(
                    $selectedTeacher->id, 
                    $request->input('exam_id')
                );
                
                // Separate passed and failed students
                $passedStudents = array_filter($subjectResults, function($student) {
                    return $student['is_passed'] === true;
                });
                
                $failedStudents = array_filter($subjectResults, function($student) {
                    return $student['is_passed'] === false;
                });
                
                $studentsList = $subjectResults;
            }
        }

        return view('admin.exams.reports.teacher-wise-results', compact(
            'teachers',
            'selectedTeacher',
            'selectedSubject',
            'availableSubjects',
            'availableExams',
            'subjectResults',
            'subjectTotals',
            'teacherSummary',
            'exams',
            'studentsList',
            'passedStudents',
            'failedStudents',
            'debugInfo'
        ));
    }

    /**
     * Get detailed results for a specific teacher and subject - FIXED
     */
    private function getSubjectWiseResults($teacherId, $subjectId, $examId = null)
    {
        // FIXED: Simplified query - directly get marks for the subject
        $query = ExamMark::with([
            'exam.classSection',
            'student',
            'subject'
        ])
        ->where('subject_id', $subjectId)
        ->whereHas('exam', function($q) use ($examId) {
            $q->where('is_published', true);
            if ($examId) {
                $q->where('id', $examId);
            }
        });

        // If exam ID is not specified, get all exams
        $examMarks = $query->get();

        // Debug logging - FIXED: Now Log is imported
        Log::info('Subject Results Query:', [
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'exam_id' => $examId,
            'results_count' => $examMarks->count()
        ]);

        $results = [];
        foreach ($examMarks as $mark) {
            $percentage = $mark->max_marks > 0 
                ? ($mark->marks_obtained / $mark->max_marks) * 100 
                : 0;
            $isPassed = $percentage >= 40;
            $grade = $this->calculateGrade($percentage);

            $startDate = $mark->exam->start_date ? Carbon::parse($mark->exam->start_date) : null;

            $results[] = [
                'student_id' => $mark->student_id,
                'student_name' => $mark->student->first_name . ' ' . $mark->student->last_name,
                'student_email' => $mark->student->email ?? null,
                'roll_number' => $mark->student->roll_number ?? null,
                'admission_number' => $mark->student->admission_number ?? null,
                'section_name' => $mark->exam->classSection->section_name ?? 'N/A',
                'section_id' => $mark->exam->classSection->id ?? null,
                'exam_name' => $mark->exam->name,
                'exam_id' => $mark->exam_id,
                'exam_date' => $startDate ? $startDate->format('d M Y') : 'N/A',
                'marks_obtained' => $mark->marks_obtained,
                'max_marks' => $mark->max_marks,
                'passing_marks' => $mark->passing_marks ?? 0,
                'percentage' => $percentage,
                'grade' => $grade,
                'is_passed' => $isPassed,
                'remarks' => $mark->remarks ?? null,
                'subject_name' => $mark->subject->name ?? 'N/A',
                'subject_code' => $mark->subject->code ?? 'N/A'
            ];
        }

        return $results;
    }

    /**
     * Calculate totals for subject results
     */
    private function calculateSubjectTotals($results)
    {
        if (empty($results)) {
            return [
                'total_students' => 0,
                'total_passed' => 0,
                'total_failed' => 0,
                'pass_percentage' => 0,
                'average_marks' => 0,
                'average_max_marks' => 0,
                'grade_distribution' => [],
                'highest_marks' => 0,
                'lowest_marks' => 0,
                'total_marks' => 0,
                'passed_list' => [],
                'failed_list' => []
            ];
        }

        $totals = [
            'total_students' => 0,
            'total_passed' => 0,
            'total_failed' => 0,
            'pass_percentage' => 0,
            'average_marks' => 0,
            'average_max_marks' => 0,
            'grade_distribution' => [],
            'highest_marks' => 0,
            'lowest_marks' => PHP_INT_MAX,
            'total_marks' => 0,
            'total_max_marks' => 0,
            'passed_list' => [],
            'failed_list' => []
        ];

        foreach ($results as $result) {
            $totals['total_students']++;
            $totals['total_marks'] += $result['marks_obtained'];
            $totals['total_max_marks'] += $result['max_marks'];

            if ($result['is_passed']) {
                $totals['total_passed']++;
                $totals['passed_list'][] = $result['student_name'];
            } else {
                $totals['total_failed']++;
                $totals['failed_list'][] = $result['student_name'];
            }

            if ($result['marks_obtained'] > $totals['highest_marks']) {
                $totals['highest_marks'] = $result['marks_obtained'];
            }
            if ($result['marks_obtained'] < $totals['lowest_marks']) {
                $totals['lowest_marks'] = $result['marks_obtained'];
            }

            $grade = $result['grade'];
            if (!isset($totals['grade_distribution'][$grade])) {
                $totals['grade_distribution'][$grade] = 0;
            }
            $totals['grade_distribution'][$grade]++;
        }

        if ($totals['total_students'] > 0) {
            $totals['pass_percentage'] = ($totals['total_passed'] / $totals['total_students']) * 100;
            $totals['average_marks'] = $totals['total_marks'] / $totals['total_students'];
            $totals['average_max_marks'] = $totals['total_max_marks'] / $totals['total_students'];
        }

        if ($totals['lowest_marks'] == PHP_INT_MAX) {
            $totals['lowest_marks'] = 0;
        }

        ksort($totals['grade_distribution']);

        return $totals;
    }

    /**
     * Get teacher summary statistics
     */
    private function getTeacherSummary($teacherId, $examId = null)
    {
        $teacherSubjects = TeacherSubject::where('teacher_id', $teacherId)
                                        ->pluck('subject_id')
                                        ->toArray();

        if (empty($teacherSubjects)) {
            return collect([]);
        }

        $query = ExamMark::select(
            'subjects.name as subject_name',
            'subjects.id as subject_id',
            'subjects.code as subject_code',
            DB::raw('COUNT(DISTINCT students.id) as total_students'),
            DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
            DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
            DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks'),
            DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= 40 THEN 1 ELSE 0 END) as passed_count'),
            DB::raw('COUNT(*) as total_marks_entries')
        )
        ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
        ->join('students', 'exam_marks.student_id', '=', 'students.id')
        ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
        ->whereIn('exam_marks.subject_id', $teacherSubjects)
        ->where('exams.is_published', true);

        if ($examId) {
            $query->where('exam_marks.exam_id', $examId);
        }

        $summary = $query->groupBy('subjects.name', 'subjects.id', 'subjects.code')
                        ->orderBy('subjects.name')
                        ->get();

        foreach ($summary as $item) {
            $item->pass_percentage = $item->total_students > 0 
                ? ($item->passed_count / $item->total_students) * 100 
                : 0;
        }

        return $summary;
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    /**
     * Get subjects for a specific teacher (AJAX)
     */
    public function getTeacherSubjects(Request $request)
    {
        $teacherId = $request->teacher_id;
        
        if (!$teacherId) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher ID is required'
            ], 400);
        }

        try {
            $teacherSubjects = TeacherSubject::where('teacher_id', $teacherId)
                                            ->with('subject')
                                            ->get();
            
            $teacher = Staff::find($teacherId);
            
            $subjects = [];
            foreach ($teacherSubjects as $ts) {
                if ($ts->subject) {
                    $hasMarks = ExamMark::where('subject_id', $ts->subject->id)
                        ->whereHas('exam', function($q) {
                            $q->where('is_published', true);
                        })
                        ->exists();
                    
                    $subjects[] = [
                        'id' => $ts->subject->id,
                        'name' => $ts->subject->name,
                        'code' => $ts->subject->code ?? 'N/A',
                        'has_results' => $hasMarks
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'teacher' => $teacher ? $teacher->first_name . ' ' . $teacher->last_name : 'Unknown',
                'subjects' => $subjects
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export teacher-subject results to CSV
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:staff,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $teacherId = $request->teacher_id;
        $subjectId = $request->subject_id;
        $examId = $request->exam_id;

        $teacher = Staff::findOrFail($teacherId);
        $subject = Subject::findOrFail($subjectId);
        $results = $this->getSubjectWiseResults($teacherId, $subjectId, $examId);
        $totals = $this->calculateSubjectTotals($results);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="teacher_subject_results_' . 
                str_replace(' ', '_', $teacher->first_name . '_' . $teacher->last_name) . '_' . 
                str_replace(' ', '_', $subject->name) . '_' . 
                date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($results, $totals, $teacher, $subject) {
            $handle = fopen('php://output', 'w');
            
            fputs($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['Teacher-Subject Exam Results Report']);
            fputcsv($handle, ['Teacher:', $teacher->first_name . ' ' . $teacher->last_name]);
            fputcsv($handle, ['Employee ID:', $teacher->employee_id ?? 'N/A']);
            fputcsv($handle, ['Subject:', $subject->name . ' (' . ($subject->code ?? 'N/A') . ')']);
            fputcsv($handle, ['Report Date:', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['SUMMARY STATISTICS']);
            fputcsv($handle, [
                'Total Students',
                'Passed',
                'Failed',
                'Pass Percentage',
                'Average Marks',
                'Highest Marks',
                'Lowest Marks'
            ]);
            fputcsv($handle, [
                $totals['total_students'],
                $totals['total_passed'],
                $totals['total_failed'],
                number_format($totals['pass_percentage'], 2) . '%',
                number_format($totals['average_marks'], 2),
                $totals['highest_marks'],
                $totals['lowest_marks']
            ]);
            fputcsv($handle, []);
            
            if (!empty($totals['grade_distribution'])) {
                fputcsv($handle, ['GRADE DISTRIBUTION']);
                fputcsv($handle, ['Grade', 'Count', 'Percentage']);
                foreach ($totals['grade_distribution'] as $grade => $count) {
                    $percentage = ($count / $totals['total_students']) * 100;
                    fputcsv($handle, [
                        $grade,
                        $count,
                        number_format($percentage, 2) . '%'
                    ]);
                }
                fputcsv($handle, []);
            }
            
            fputcsv($handle, ['PASSED STUDENTS']);
            fputcsv($handle, ['S.No', 'Student Name', 'Roll No', 'Marks', 'Percentage', 'Grade']);
            $counter = 1;
            foreach ($results as $result) {
                if ($result['is_passed']) {
                    fputcsv($handle, [
                        $counter++,
                        $result['student_name'],
                        $result['roll_number'] ?? 'N/A',
                        $result['marks_obtained'],
                        number_format($result['percentage'], 2) . '%',
                        $result['grade']
                    ]);
                }
            }
            
            fputcsv($handle, []);
            fputcsv($handle, ['FAILED STUDENTS']);
            fputcsv($handle, ['S.No', 'Student Name', 'Roll No', 'Marks', 'Percentage', 'Grade']);
            $counter = 1;
            foreach ($results as $result) {
                if (!$result['is_passed']) {
                    fputcsv($handle, [
                        $counter++,
                        $result['student_name'],
                        $result['roll_number'] ?? 'N/A',
                        $result['marks_obtained'],
                        number_format($result['percentage'], 2) . '%',
                        $result['grade']
                    ]);
                }
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API endpoint for AJAX requests - get detailed results
     */
    public function apiGetResults(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:staff,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $results = $this->getSubjectWiseResults(
            $request->teacher_id,
            $request->subject_id,
            $request->exam_id
        );

        $totals = $this->calculateSubjectTotals($results);

        return response()->json([
            'success' => true,
            'data' => [
                'results' => $results,
                'totals' => $totals
            ]
        ]);
    }

    /**
     * Get all exams for a teacher's subject (for AJAX)
     */
    public function getTeacherSubjectExams(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:staff,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // FIXED: Get exams that have marks for this subject
        $examIds = DB::table('exam_marks')
            ->where('subject_id', $request->subject_id)
            ->distinct()
            ->pluck('exam_id')
            ->toArray();

        $exams = Exam::where('is_published', true)
            ->whereIn('id', $examIds)
            ->with('classSection')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'exams' => $exams->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name . ($exam->classSection ? ' - ' . $exam->classSection->section_name : ''),
                    'date' => $exam->start_date ? Carbon::parse($exam->start_date)->format('d M Y') : 'N/A'
                ];
            })
        ]);
    }

    /**
     * Debug endpoint - Check teacher marks directly
     */
    public function debugTeacherMarks($teacherId)
    {
        $teacher = Staff::find($teacherId);
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }
        
        $teacherSubjects = TeacherSubject::where('teacher_id', $teacherId)
                                        ->with('subject')
                                        ->get();
        
        $results = [];
        foreach ($teacherSubjects as $ts) {
            if ($ts->subject) {
                $marks = ExamMark::where('subject_id', $ts->subject->id)
                    ->with(['student', 'exam'])
                    ->get();
                
                $results[] = [
                    'subject_id' => $ts->subject->id,
                    'subject_name' => $ts->subject->name,
                    'marks_count' => $marks->count(),
                    'students' => $marks->map(function($m) {
                        return [
                            'student' => $m->student ? $m->student->first_name . ' ' . $m->student->last_name : 'Unknown',
                            'exam' => $m->exam ? $m->exam->name : 'Unknown',
                            'exam_date' => $m->exam && $m->exam->start_date ? Carbon::parse($m->exam->start_date)->format('d M Y') : 'N/A',
                            'marks' => $m->marks_obtained,
                            'max_marks' => $m->max_marks,
                            'percentage' => $m->max_marks > 0 ? round(($m->marks_obtained / $m->max_marks) * 100, 2) : 0
                        ];
                    })
                ];
            }
        }
        
        return response()->json([
            'teacher' => $teacher->first_name . ' ' . $teacher->last_name,
            'subjects' => $results
        ]);
    }

    /**
     * Get all teachers with their subjects for debugging
     */
    public function debugAllTeachers()
    {
        $teachers = Staff::where('is_teacher', true)->get();
        
        $result = [];
        foreach ($teachers as $teacher) {
            $subjects = TeacherSubject::where('teacher_id', $teacher->id)
                                    ->with('subject')
                                    ->get();
            
            $result[] = [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->first_name . ' ' . $teacher->last_name,
                'subject_count' => $subjects->count(),
                'subjects' => $subjects->map(function($ts) {
                    return $ts->subject ? $ts->subject->name : null;
                })->filter()->values()
            ];
        }
        
        return response()->json($result);
    }
}