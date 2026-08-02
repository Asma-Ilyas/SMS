<?php
// app/Traits/AttendancePermissionTrait.php

namespace App\Traits;

use App\Models\AttendanceTeacherSection;
use App\Models\ClassSection;
use App\Models\CommonSubjectClass;
use App\Models\Staff;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\TimetableEntry;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait AttendancePermissionTrait
{
    public function canMarkAttendance($teacherId, $classSectionId, $subjectId = null)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return true;
        }

        $teacherId = $teacherId instanceof Staff ? $teacherId->id : $teacherId;

        $query = AttendanceTeacherSection::where('teacher_id', $teacherId)
            ->where('class_section_id', $classSectionId)
            ->where('can_mark_attendance', true);

        if ($subjectId) {
            $query->where(function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId)->orWhereNull('subject_id');
            });
        }

        if ($query->exists()) {
            return true;
        }

        return $this->isTeacherAssignedToSection($teacherId, $classSectionId, $subjectId);
    }

    public function canMarkAttendanceForDate($teacherId, $classSectionId, $date, $subjectId = null)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return true;
        }

        $teacherId = $teacherId instanceof Staff ? $teacherId->id : $teacherId;
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        $query = AttendanceTeacherSection::where('teacher_id', $teacherId)
            ->where('class_section_id', $classSectionId)
            ->where('can_mark_attendance', true);

        if ($subjectId) {
            $query->where(function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId)->orWhereNull('subject_id');
            });
        }

        if ($query->exists()) {
            return true;
        }

        $firstTimeSlot = TimeSlot::where('type', 'period')->orderBy('sort_order')->first();
        if ($firstTimeSlot) {
            $timetableEntry = TimetableEntry::where('class_section_id', $classSectionId)
                ->where('day_of_week', $dayOfWeek)
                ->where('time_slot_id', $firstTimeSlot->id)
                ->first();

            if ($timetableEntry && $timetableEntry->teacher_id == $teacherId) {
                if (!$subjectId || $timetableEntry->subject_id == $subjectId) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canApproveLeave($teacherId, $classSectionId = null)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return true;
        }

        $teacherId = $teacherId instanceof Staff ? $teacherId->id : $teacherId;
        $query = AttendanceTeacherSection::where('teacher_id', $teacherId)->where('can_approve_leave', true);

        if ($classSectionId) {
            $query->where('class_section_id', $classSectionId);
        }

        return $query->exists();
    }

    public function getAuthorizedSections($teacherId)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return ClassSection::with('class.grade', 'class.stream')->get();
        }

        $teacherId = $teacherId instanceof Staff ? $teacherId->id : $teacherId;
        $allSectionIds = [];

        $assignedSections = AttendanceTeacherSection::where('teacher_id', $teacherId)
            ->where('can_mark_attendance', true)
            ->pluck('class_section_id')
            ->toArray();
        $allSectionIds = array_merge($allSectionIds, $assignedSections);

        $today = Carbon::today();
        $dayOfWeek = strtolower($today->format('l'));
        $firstTimeSlot = TimeSlot::where('type', 'period')->orderBy('sort_order')->first();

        if ($firstTimeSlot) {
            $timetableSections = TimetableEntry::where('day_of_week', $dayOfWeek)
                ->where('time_slot_id', $firstTimeSlot->id)
                ->where('teacher_id', $teacherId)
                ->pluck('class_section_id')
                ->toArray();
            $allSectionIds = array_merge($allSectionIds, $timetableSections);
        }

        $allSectionIds = array_unique($allSectionIds);
        return ClassSection::whereIn('id', $allSectionIds)->with('class.grade', 'class.stream')->get();
    }

    public function getAuthorizedSubjects($teacherId, $classSectionId)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return Subject::whereHas('classSections', function($q) use ($classSectionId) {
                $q->where('class_section_id', $classSectionId);
            })->get();
        }

        $teacherId = $teacherId instanceof Staff ? $teacherId->id : $teacherId;
        $allSubjectIds = [];

        $assignedSubjects = AttendanceTeacherSection::where('teacher_id', $teacherId)
            ->where('class_section_id', $classSectionId)
            ->where('can_mark_attendance', true)
            ->whereNotNull('subject_id')
            ->pluck('subject_id')
            ->toArray();
        $allSubjectIds = array_merge($allSubjectIds, $assignedSubjects);

        $today = Carbon::today();
        $dayOfWeek = strtolower($today->format('l'));
        $firstTimeSlot = TimeSlot::where('type', 'period')->orderBy('sort_order')->first();

        if ($firstTimeSlot) {
            $timetableEntry = TimetableEntry::where('class_section_id', $classSectionId)
                ->where('day_of_week', $dayOfWeek)
                ->where('time_slot_id', $firstTimeSlot->id)
                ->where('teacher_id', $teacherId)
                ->first();

            if ($timetableEntry) {
                $allSubjectIds[] = $timetableEntry->subject_id;
            }
        }

        $allSubjectIds = array_unique($allSubjectIds);
        return Subject::whereIn('id', $allSubjectIds)->get();
    }

    public function getCommonSubjectClasses($classSectionId)
    {
        return CommonSubjectClass::whereJsonContains('class_section_ids', (string)$classSectionId)
            ->where('is_active', true)
            ->with(['subject', 'room'])
            ->get();
    }

    public function getPendingLeaveRequests($teacherId)
    {
        $teacherId = $teacherId instanceof Staff ? $teacherId->id : $teacherId;
        $authorizedSections = $this->getAuthorizedSections($teacherId)->pluck('id')->toArray();

        return StudentAttendance::whereIn('class_section_id', $authorizedSections)
            ->where('status', 'absent')
            ->where('is_approved', false)
            ->whereNotNull('leave_reason')
            ->with(['student', 'classSection.class'])
            ->orderBy('date', 'desc')
            ->get();
    }

    protected function isTeacherAssignedToSection($teacherId, $classSectionId, $subjectId = null)
    {
        $today = Carbon::today();
        $dayOfWeek = strtolower($today->format('l'));
        $firstTimeSlot = TimeSlot::where('type', 'period')->orderBy('sort_order')->first();

        if ($firstTimeSlot) {
            $query = TimetableEntry::where('class_section_id', $classSectionId)
                ->where('day_of_week', $dayOfWeek)
                ->where('time_slot_id', $firstTimeSlot->id)
                ->where('teacher_id', $teacherId);

            if ($subjectId) {
                $query->where('subject_id', $subjectId);
            }

            return $query->exists();
        }

        return false;
    }
}