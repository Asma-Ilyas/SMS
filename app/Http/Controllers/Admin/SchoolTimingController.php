<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolTiming;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolTimingController extends Controller
{
    /**
     * Display a listing of school timings.
     */
    public function index()
    {
        $timings = SchoolTiming::with('timeSlots')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.timings.index', compact('timings'));
    }

    /**
     * Show the form for creating a new school timing.
     */
    public function create()
    {
        return view('admin.timings.create');
    }

    /**
     * Store a newly created school timing in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string|max:255',
            'school_start' => 'required|date_format:H:i',
            'school_end' => 'required|date_format:H:i|after:school_start',
            'period_duration' => 'required|integer|min:30|max:90',
            'session_start' => 'required|date',
            'session_end' => 'required|date|after:session_start',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // If this is active, deactivate others first
            if ($request->has('is_active')) {
                SchoolTiming::where('is_active', true)->update(['is_active' => false]);
            }

            $timing = SchoolTiming::create([
                'session_name' => $request->session_name,
                'school_start' => $request->school_start . ':00',
                'school_end' => $request->school_end . ':00',
                'period_duration' => $request->period_duration,
                'session_start' => $request->session_start,
                'session_end' => $request->session_end,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            // Generate default time slots
            $this->generateDefaultSlots($timing);

            DB::commit();

            return redirect()->route('admin.school-timings.index')
                ->with('success', 'School timing created successfully with ' . $timing->timeSlots->count() . ' time slots.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating school timing: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create school timing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified school timing.
     */
    public function show(SchoolTiming $schoolTiming)
    {
        $timeSlots = $schoolTiming->timeSlots()
            ->orderBy('sort_order')
            ->get();
            
        return view('admin.timings.show', compact('schoolTiming', 'timeSlots'));
    }

    /**
     * Show the form for editing the specified school timing.
     */
    public function edit(SchoolTiming $schoolTiming)
    {
        return view('admin.timings.edit', compact('schoolTiming'));
    }

    /**
     * Update the specified school timing in storage.
     */
    public function update(Request $request, SchoolTiming $schoolTiming)
    {
        $request->validate([
            'session_name' => 'required|string|max:255',
            'school_start' => 'required|date_format:H:i',
            'school_end' => 'required|date_format:H:i|after:school_start',
            'period_duration' => 'required|integer|min:30|max:90',
            'session_start' => 'required|date',
            'session_end' => 'required|date|after:session_start',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // If this is active, deactivate others
            if ($request->has('is_active')) {
                SchoolTiming::where('id', '!=', $schoolTiming->id)->where('is_active', true)->update(['is_active' => false]);
            }

            $schoolTiming->update([
                'session_name' => $request->session_name,
                'school_start' => $request->school_start . ':00',
                'school_end' => $request->school_end . ':00',
                'period_duration' => $request->period_duration,
                'session_start' => $request->session_start,
                'session_end' => $request->session_end,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()->route('admin.school-timings.index')
                ->with('success', 'School timing updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating school timing: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update school timing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified school timing from storage.
     */
    public function destroy(SchoolTiming $schoolTiming)
    {
        try {
            DB::beginTransaction();

            // Delete associated time slots
            $schoolTiming->timeSlots()->delete();
            
            // Delete the timing
            $schoolTiming->delete();

            DB::commit();

            return redirect()->route('admin.school-timings.index')
                ->with('success', 'School timing deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting school timing: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete school timing: ' . $e->getMessage());
        }
    }

    /**
     * Activate a timing session.
     */
    public function activate(SchoolTiming $schoolTiming)
    {
        try {
            // Deactivate all other timings
            SchoolTiming::where('is_active', true)->update(['is_active' => false]);
            
            // Activate this one
            $schoolTiming->update(['is_active' => true]);
            
            return redirect()->back()
                ->with('success', 'Session "' . $schoolTiming->session_name . '" activated successfully.');

        } catch (\Exception $e) {
            Log::error('Error activating school timing: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to activate session: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate time slots for a timing session.
     */
    public function regenerate(SchoolTiming $schoolTiming)
    {
        try {
            DB::beginTransaction();
            
            // Delete existing time slots
            $schoolTiming->timeSlots()->delete();
            
            // Regenerate default time slots
            $this->generateDefaultSlots($schoolTiming);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Time slots regenerated successfully for "' . $schoolTiming->session_name . '".');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error regenerating time slots: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to regenerate slots: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a timing session.
     */
    public function duplicate(SchoolTiming $schoolTiming)
    {
        try {
            DB::beginTransaction();
            
            // Duplicate the timing
            $newTiming = $schoolTiming->replicate();
            $newTiming->session_name = $schoolTiming->session_name . ' (Copy)';
            $newTiming->is_active = false;
            $newTiming->save();
            
            // Duplicate time slots
            foreach ($schoolTiming->timeSlots as $slot) {
                $newSlot = $slot->replicate();
                $newSlot->school_timing_id = $newTiming->id;
                $newSlot->save();
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Session duplicated successfully. New session: "' . $newTiming->session_name . '"');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating school timing: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to duplicate session: ' . $e->getMessage());
        }
    }

    /**
     * Generate default time slots for a timing.
     */
    private function generateDefaultSlots(SchoolTiming $schoolTiming)
    {
        $startTime = $schoolTiming->school_start ?? '08:00:00';
        $endTime = $schoolTiming->school_end ?? '14:30:00';
        $periodDuration = $schoolTiming->period_duration ?? 45;
        $breakDuration = 15;
        $lunchDuration = 30;
        
        $currentTime = strtotime($startTime);
        $endTimeStamp = strtotime($endTime);
        $slotNumber = 0;
        
        // Assembly/Activity slot - use the activity from database
        $activityStart = $schoolTiming->activity_start ?? '08:00:00';
        $activityEnd = $schoolTiming->activity_end ?? '08:10:00';
        $activityLabel = $schoolTiming->activity_label ?? 'Assembly/Dua';
        
        TimeSlot::create([
            'school_timing_id' => $schoolTiming->id,
            'label' => $activityLabel,
            'start_time' => $activityStart,
            'end_time' => $activityEnd,
            'type' => 'activity',
            'sort_order' => $slotNumber,
            'is_active' => true
        ]);
        
        $slotNumber++;
        
        // Get breaks from JSON
        $breaks = json_decode($schoolTiming->breaks, true) ?? [];
        
        // Start from activity end time
        $currentTime = strtotime($activityEnd);
        
        // Period slots
        $periodCount = 0;
        while ($currentTime < $endTimeStamp && $periodCount < 8) {
            // Check if current time matches any break
            $timeStr = date('H:i', $currentTime);
            $isBreakTime = false;
            $breakLabel = '';
            $breakEnd = 0;
            
            foreach ($breaks as $break) {
                if ($timeStr == $break['start']) {
                    $isBreakTime = true;
                    $breakLabel = $break['label'] ?? 'Break';
                    $breakEnd = strtotime($break['end']);
                    break;
                }
            }
            
            if ($isBreakTime) {
                TimeSlot::create([
                    'school_timing_id' => $schoolTiming->id,
                    'label' => $breakLabel,
                    'start_time' => date('H:i:s', $currentTime),
                    'end_time' => date('H:i:s', $breakEnd),
                    'type' => 'break',
                    'sort_order' => $slotNumber,
                    'is_active' => true
                ]);
                $slotNumber++;
                $currentTime = $breakEnd;
                continue;
            }
            
            // Regular period
            $periodEnd = $currentTime + ($periodDuration * 60);
            
            // Don't exceed end time
            if ($periodEnd > $endTimeStamp) {
                $periodEnd = $endTimeStamp;
            }
            
            $periodNumber = $periodCount + 1;
            TimeSlot::create([
                'school_timing_id' => $schoolTiming->id,
                'label' => 'Period ' . $periodNumber,
                'start_time' => date('H:i:s', $currentTime),
                'end_time' => date('H:i:s', $periodEnd),
                'type' => 'period',
                'sort_order' => $slotNumber,
                'is_active' => true
            ]);
            
            $slotNumber++;
            $periodCount++;
            $currentTime = $periodEnd;
        }
    }
}