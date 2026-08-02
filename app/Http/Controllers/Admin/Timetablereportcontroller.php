<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolTiming;
use App\Models\TimeSlot;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Room;
use App\Models\TimetableEntry;
use App\Models\TimetableGenerationLog;
use App\Models\SubjectAssignment;
use App\Services\TimetableGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimetableReportController extends Controller
{
    /**
     * Display timetable reports dashboard
     */
    public function index(Request $request)
    {
        // Get ALL timings
        $timings = SchoolTiming::orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        $teachers = Staff::where('is_teacher', true)->where('is_active', true)->get();
        $rooms = Room::where('is_available', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        
        // Get the active timing
        $activeTiming = SchoolTiming::where('is_active', true)->first();
        
        // If no active timing, try to get the first one
        if (!$activeTiming && $timings->count() > 0) {
            $activeTiming = $timings->first();
        }
        
        // Get entries for active timing
        $entries = collect();
        $timeSlots = collect();
        $groupedEntries = [];
        
        if ($activeTiming) {
            $timeSlots = TimeSlot::where('school_timing_id', $activeTiming->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
                
            $entries = TimetableEntry::with(['classSection', 'subject', 'teacher', 'room', 'timeSlot'])
                ->whereHas('timeSlot', function($q) use ($activeTiming) {
                    $q->where('school_timing_id', $activeTiming->id);
                })
                ->orderBy('day_of_week')
                ->orderBy('time_slot_id')
                ->get();
                
            $groupedEntries = $entries->groupBy('day_of_week');
        }
        
        // Get generation logs
        $logs = TimetableGenerationLog::orderBy('generated_at', 'desc')->take(10)->get();
        
        return view('admin.timetable-reports.index', compact(
            'timings',
            'activeTiming',
            'classSections',
            'teachers',
            'rooms',
            'subjects',
            'timeSlots',
            'entries',
            'groupedEntries',
            'logs'
        ));
    }

    /**
     * View timetable by class
     */
    public function byClass(Request $request, $classId = null)
    {
        $classId = $classId ?? $request->class_id;
        $classes = \App\Models\Classes::with(['grade', 'stream'])->get();
        
        $selectedClass = null;
        $entries = collect();
        $timeSlots = collect();
        $activeTiming = null;
        
        // Get active timing
        $activeTiming = SchoolTiming::where('is_active', true)->first();
        
        if (!$activeTiming) {
            // Try to get any timing
            $activeTiming = SchoolTiming::first();
        }
        
        if ($classId) {
            $selectedClass = \App\Models\Classes::with(['grade', 'stream'])->find($classId);
            
            if ($activeTiming) {
                $timeSlots = TimeSlot::where('school_timing_id', $activeTiming->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
                    
                $entries = TimetableEntry::with(['classSection', 'subject', 'teacher', 'room', 'timeSlot'])
                    ->whereHas('classSection', function($q) use ($classId) {
                        $q->where('class_id', $classId);
                    })
                    ->whereHas('timeSlot', function($q) use ($activeTiming) {
                        $q->where('school_timing_id', $activeTiming->id);
                    })
                    ->orderBy('day_of_week')
                    ->orderBy('time_slot_id')
                    ->get();
            }
        }
        
        return view('admin.timetable-reports.by-class', compact(
            'classes',
            'selectedClass',
            'entries',
            'timeSlots',
            'classId',
            'activeTiming'
        ));
    }

    /**
     * View timetable by section
     */
    public function bySection(Request $request, $classSectionId = null)
    {
        $classSectionId = $classSectionId ?? $request->class_section_id;
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        
        $selectedSection = null;
        $entries = collect();
        $timeSlots = collect();
        $activeTiming = null;
        
        // Get active timing
        $activeTiming = SchoolTiming::where('is_active', true)->first();
        
        if (!$activeTiming) {
            $activeTiming = SchoolTiming::first();
        }
        
        if ($classSectionId) {
            $selectedSection = ClassSection::with(['class.grade', 'class.stream'])->find($classSectionId);
            
            if ($activeTiming) {
                $timeSlots = TimeSlot::where('school_timing_id', $activeTiming->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
                    
                $entries = TimetableEntry::with(['subject', 'teacher', 'room', 'timeSlot'])
                    ->where('class_section_id', $classSectionId)
                    ->whereHas('timeSlot', function($q) use ($activeTiming) {
                        $q->where('school_timing_id', $activeTiming->id);
                    })
                    ->orderBy('day_of_week')
                    ->orderBy('time_slot_id')
                    ->get();
            }
        }
        
        return view('admin.timetable-reports.by-section', compact(
            'classSections',
            'selectedSection',
            'entries',
            'timeSlots',
            'classSectionId',
            'activeTiming'
        ));
    }

    /**
     * View timetable by teacher
     */
    public function byTeacher(Request $request, $teacherId = null)
    {
        $teacherId = $teacherId ?? $request->teacher_id;
        $teachers = Staff::where('is_teacher', true)->where('is_active', true)->get();
        
        $selectedTeacher = null;
        $entries = collect();
        $timeSlots = collect();
        $activeTiming = null;
        
        // Get active timing
        $activeTiming = SchoolTiming::where('is_active', true)->first();
        
        if (!$activeTiming) {
            $activeTiming = SchoolTiming::first();
        }
        
        if ($teacherId) {
            $selectedTeacher = Staff::find($teacherId);
            
            if ($activeTiming) {
                $timeSlots = TimeSlot::where('school_timing_id', $activeTiming->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
                    
                $entries = TimetableEntry::with(['classSection', 'subject', 'room', 'timeSlot'])
                    ->where('teacher_id', $teacherId)
                    ->whereHas('timeSlot', function($q) use ($activeTiming) {
                        $q->where('school_timing_id', $activeTiming->id);
                    })
                    ->orderBy('day_of_week')
                    ->orderBy('time_slot_id')
                    ->get();
            }
        }
        
        return view('admin.timetable-reports.by-teacher', compact(
            'teachers',
            'selectedTeacher',
            'entries',
            'timeSlots',
            'teacherId',
            'activeTiming'
        ));
    }

    /**
     * Print timetable
     */
    public function print(Request $request)
    {
        $classSectionId = $request->class_section_id;
        $teacherId = $request->teacher_id;
        
        $entries = collect();
        $timeSlots = collect();
        $title = 'Timetable';
        $activeTiming = SchoolTiming::where('is_active', true)->first();
        
        if (!$activeTiming) {
            $activeTiming = SchoolTiming::first();
        }
        
        if ($activeTiming) {
            $timeSlots = TimeSlot::where('school_timing_id', $activeTiming->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }
        
        if ($classSectionId) {
            $section = ClassSection::with(['class.grade', 'class.stream'])->find($classSectionId);
            $title = 'Timetable - ' . ($section->full_name ?? 'Class');
            
            $entries = TimetableEntry::with(['subject', 'teacher', 'room', 'timeSlot'])
                ->where('class_section_id', $classSectionId)
                ->whereHas('timeSlot', function($q) use ($activeTiming) {
                    $q->where('school_timing_id', $activeTiming->id ?? 0);
                })
                ->orderBy('day_of_week')
                ->orderBy('time_slot_id')
                ->get();
        } elseif ($teacherId) {
            $teacher = Staff::find($teacherId);
            $title = 'Timetable - ' . ($teacher->full_name ?? 'Teacher');
            
            $entries = TimetableEntry::with(['classSection', 'subject', 'room', 'timeSlot'])
                ->where('teacher_id', $teacherId)
                ->whereHas('timeSlot', function($q) use ($activeTiming) {
                    $q->where('school_timing_id', $activeTiming->id ?? 0);
                })
                ->orderBy('day_of_week')
                ->orderBy('time_slot_id')
                ->get();
        }
        
        return view('admin.timetable-reports.print', compact(
            'entries',
            'timeSlots',
            'title',
            'activeTiming'
        ));
    }

    /**
     * Export timetable to CSV
     */
    public function export(Request $request)
    {
        $classSectionId = $request->class_section_id;
        $teacherId = $request->teacher_id;
        
        $entries = collect();
        
        if ($classSectionId) {
            $entries = TimetableEntry::with(['subject', 'teacher', 'room', 'timeSlot'])
                ->where('class_section_id', $classSectionId)
                ->orderBy('day_of_week')
                ->orderBy('time_slot_id')
                ->get();
        } elseif ($teacherId) {
            $entries = TimetableEntry::with(['classSection', 'subject', 'room', 'timeSlot'])
                ->where('teacher_id', $teacherId)
                ->orderBy('day_of_week')
                ->orderBy('time_slot_id')
                ->get();
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="timetable-export.csv"',
        ];
        
        $callback = function() use ($entries) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, ['Day', 'Time', 'Class', 'Subject', 'Teacher', 'Room']);
            
            foreach ($entries as $entry) {
                fputcsv($handle, [
                    ucfirst($entry->day_of_week),
                    $entry->timeSlot->label ?? 'N/A',
                    $entry->classSection->full_name ?? 'N/A',
                    $entry->subject->name ?? 'N/A',
                    $entry->teacher->full_name ?? 'N/A',
                    $entry->room->name ?? 'N/A',
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate timetable entries
     */
    public function generate(Request $request)
    {
        $request->validate([
            'timing_id' => 'required|exists:school_timings,id',
        ]);

        $timing = SchoolTiming::findOrFail($request->timing_id);

        try {
            $generator = new TimetableGeneratorService($timing);
            $result = $generator->generate();

            TimetableGenerationLog::create([
                'generated_at' => now(),
                'total_entries' => $result['summary']['entries_created'] ?? 0,
                'conflicts_resolved' => $result['summary']['conflicts_resolved'] ?? 0,
                'errors' => json_encode($result['failures'] ?? []),
            ]);

            $redirectRoute = 'admin.timetable-reports.index';

            if (!$result['success']) {
                $errorMsg = implode('; ', array_slice($result['failures'], 0, 3));
                return redirect()->route($redirectRoute)
                    ->with('error', 'Timetable generation failed: ' . $errorMsg);
            }

            $msg = "✅ Generated {$result['summary']['entries_created']} timetable entries.";
            if (!empty($result['failures'])) {
                $msg .= " ⚠️ " . count($result['failures']) . " warnings occurred.";
                return redirect()->route($redirectRoute)
                    ->with('warning', $msg);
            }

            return redirect()->route($redirectRoute)
                ->with('success', $msg);

        } catch (\Exception $e) {
            Log::error('Timetable generation failed: ' . $e->getMessage());
            return redirect()->route('admin.timetable-reports.index')
                ->with('error', 'Failed to generate timetable: ' . $e->getMessage());
        }
    }

    /**
     * Show auto generate form
     */
    public function showAutoGenerate()
    {
        $timings = SchoolTiming::orderBy('session_start', 'desc')->get();
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        
        return view('admin.timetable-reports.auto-generate', compact('timings', 'classSections'));
    }

    /**
     * View generation logs
     */
    public function logs()
    {
        $logs = TimetableGenerationLog::orderBy('generated_at', 'desc')->paginate(20);
        return view('admin.timetable-reports.logs', compact('logs'));
    }

    /**
     * Show no active timing message
     */
    public function noActive()
    {
        return view('admin.timetable-reports.no-active');
    }

    /**
     * Edit timetable entry data
     */
    public function editData($id)
    {
        $entry = TimetableEntry::with(['classSection', 'subject', 'teacher', 'room', 'timeSlot'])
            ->findOrFail($id);
            
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Staff::where('is_teacher', true)->where('is_active', true)->get();
        $rooms = Room::where('is_available', true)->get();
        $timeSlots = TimeSlot::where('is_active', true)->get();
        
        return response()->json([
            'id' => $entry->id,
            'subject_id' => $entry->subject_id,
            'teacher_id' => $entry->teacher_id,
            'room_id' => $entry->room_id,
            'time_slot_id' => $entry->time_slot_id,
            'day_of_week' => $entry->day_of_week,
        ]);
    }

    /**
     * Update timetable entry
     */
    public function updateEntry(Request $request, $id)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
            'room_id' => 'required|exists:rooms,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
        ]);

        $entry = TimetableEntry::findOrFail($id);
        
        // Check for conflicts
        $conflict = TimetableEntry::where('id', '!=', $id)
            ->where('day_of_week', $request->day_of_week)
            ->where('time_slot_id', $request->time_slot_id)
            ->where(function($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id)
                  ->orWhere('room_id', $request->room_id)
                  ->orWhere('class_section_id', $request->class_section_id ?? 0);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Conflict detected! Please check teacher, room, or class availability.'
            ], 422);
        }

        $entry->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Entry updated successfully.'
        ]);
    }

    /**
     * Edit section timetable
     */
    public function editSectionTimetable($classSectionId)
    {
        $section = ClassSection::with(['class.grade', 'class.stream'])->findOrFail($classSectionId);
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Staff::where('is_teacher', true)->where('is_active', true)->get();
        $rooms = Room::where('is_available', true)->get();
        
        $activeTiming = SchoolTiming::where('is_active', true)->first();
        
        if (!$activeTiming) {
            $activeTiming = SchoolTiming::first();
        }
        
        $timeSlots = collect();
        
        if ($activeTiming) {
            $timeSlots = TimeSlot::where('school_timing_id', $activeTiming->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }
        
        $entries = TimetableEntry::where('class_section_id', $classSectionId)
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get()
            ->groupBy('day_of_week');
        
        return view('admin.timetable-reports.section-edit', compact(
            'section',
            'subjects',
            'teachers',
            'rooms',
            'timeSlots',
            'entries',
            'activeTiming'
        ));
    }

    /**
     * Update section timetable
     */
    public function updateSectionTimetable(Request $request, $classSectionId)
    {
        $request->validate([
            'entries' => 'required|array',
            'entries.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'entries.*.time_slot_id' => 'required|exists:time_slots,id',
            'entries.*.subject_id' => 'nullable|exists:subjects,id',
            'entries.*.teacher_id' => 'nullable|exists:staff,id',
            'entries.*.room_id' => 'nullable|exists:rooms,id',
        ]);

        try {
            DB::beginTransaction();

            TimetableEntry::where('class_section_id', $classSectionId)->delete();

            foreach ($request->entries as $entryData) {
                if (!empty($entryData['subject_id']) && !empty($entryData['teacher_id']) && !empty($entryData['room_id'])) {
                    TimetableEntry::create([
                        'class_section_id' => $classSectionId,
                        'subject_id' => $entryData['subject_id'],
                        'teacher_id' => $entryData['teacher_id'],
                        'room_id' => $entryData['room_id'],
                        'time_slot_id' => $entryData['time_slot_id'],
                        'day_of_week' => $entryData['day_of_week'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.timetable-reports.section', ['class_section_id' => $classSectionId])
                ->with('success', 'Timetable updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update timetable: ' . $e->getMessage());
        }
    }
}