<?php

namespace App\Http\Controllers\Admin;

use App\Models\SchoolTiming;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeSlotController extends Controller
{
    public function index(Request $request, SchoolTiming $schoolTiming)
    {
        $slots = $schoolTiming->timeSlots()
            ->when($request->search, fn($q, $s) =>
                $q->where('label', 'like', "%{$s}%")->orWhere('type', 'like', "%{$s}%")
            )
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->ordered()
            ->paginate(30)
            ->withQueryString();

        return view('admin.slots.index', compact('schoolTiming', 'slots'));
    }

    public function create(SchoolTiming $schoolTiming)
    {
        return view('admin.slots.create', compact('schoolTiming'));
    }

    public function store(Request $request, SchoolTiming $schoolTiming)
    {
        $data = $request->validate([
            'label'         => ['required', 'string', 'max:80'],
            'start_time'    => ['required', 'string'],
            'end_time'      => ['required', 'string'],
            'type'          => ['required', 'in:period,break,activity'],
            'period_number' => ['nullable', 'integer', 'min:1'],
            'sort_order'    => ['nullable', 'integer', 'min:1'],
            'is_active'     => ['boolean'],
        ]);

        // Normalize times (convert any format to HH:MM)
        $data['start_time'] = $this->normalizeTime($data['start_time']);
        $data['end_time']   = $this->normalizeTime($data['end_time']);

        // Validate times after conversion
        if (!$this->isValidTime($data['start_time'])) {
            return back()->withErrors(['start_time' => 'Invalid start time. Use HH:MM (e.g., 08:30).'])->withInput();
        }
        if (!$this->isValidTime($data['end_time'])) {
            return back()->withErrors(['end_time' => 'Invalid end time. Use HH:MM (e.g., 08:30).'])->withInput();
        }
        if ($data['end_time'] <= $data['start_time']) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $data['school_timing_id'] = $schoolTiming->id;
        if (empty($data['sort_order'])) {
            $data['sort_order'] = ($schoolTiming->timeSlots()->max('sort_order') ?? 0) + 1;
        }

        TimeSlot::create($data);

        return redirect()
            ->route('admin.slots.index', $schoolTiming)
            ->with('success', 'Slot added successfully.');
    }

    public function edit(SchoolTiming $schoolTiming, TimeSlot $timeSlot)
    {
        abort_unless($timeSlot->school_timing_id === $schoolTiming->id, 404);
        return view('admin.slots.edit', compact('schoolTiming', 'timeSlot'));
    }

    public function update(Request $request, SchoolTiming $schoolTiming, TimeSlot $timeSlot)
    {
        abort_unless($timeSlot->school_timing_id === $schoolTiming->id, 404);

        $data = $request->validate([
            'label'         => ['required', 'string', 'max:80'],
            'start_time'    => ['required', 'string'],
            'end_time'      => ['required', 'string'],
            'type'          => ['required', 'in:period,break,activity'],
            'period_number' => ['nullable', 'integer', 'min:1'],
            'sort_order'    => ['nullable', 'integer', 'min:1'],
            'is_active'     => ['boolean'],
        ]);

        $data['start_time'] = $this->normalizeTime($data['start_time']);
        $data['end_time']   = $this->normalizeTime($data['end_time']);

        if (!$this->isValidTime($data['start_time'])) {
            return back()->withErrors(['start_time' => 'Invalid start time. Use HH:MM (e.g., 08:30).'])->withInput();
        }
        if (!$this->isValidTime($data['end_time'])) {
            return back()->withErrors(['end_time' => 'Invalid end time. Use HH:MM (e.g., 08:30).'])->withInput();
        }
        if ($data['end_time'] <= $data['start_time']) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $timeSlot->update($data);

        return redirect()
            ->route('admin.slots.index', $schoolTiming)
            ->with('success', "Slot \"{$timeSlot->label}\" updated.");
    }

    public function toggle(SchoolTiming $schoolTiming, TimeSlot $timeSlot)
    {
        abort_unless($timeSlot->school_timing_id === $schoolTiming->id, 404);
        $timeSlot->update(['is_active' => !$timeSlot->is_active]);
        return back()->with('success', "Slot \"{$timeSlot->label}\" " . ($timeSlot->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroy(SchoolTiming $schoolTiming, TimeSlot $timeSlot)
    {
        abort_unless($timeSlot->school_timing_id === $schoolTiming->id, 404);
        $timeSlot->delete();
        return back()->with('success', 'Slot deleted.');
    }

    public function reorder(Request $request, SchoolTiming $schoolTiming)
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($request->order as $sortOrder => $slotId) {
            TimeSlot::where('id', $slotId)
                    ->where('school_timing_id', $schoolTiming->id)
                    ->update(['sort_order' => $sortOrder + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Convert any time format to HH:MM (24-hour)
     * Accepts: 8:30, 08:30, 8:30 AM, 08:30 AM, 8:30am, 08:30am
     */
    private function normalizeTime($time)
    {
        $time = trim($time);
        // If already HH:MM, return as is
        if (preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return $time;
        }
        // Try to parse using strtotime (handles many formats)
        $timestamp = strtotime($time);
        if ($timestamp !== false) {
            return date('H:i', $timestamp);
        }
        // Last resort: extract hours and minutes using regex
        if (preg_match('/([0-9]{1,2}):([0-9]{2})/', $time, $matches)) {
            $hour = (int)$matches[1];
            $minute = (int)$matches[2];
            if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                return sprintf('%02d:%02d', $hour, $minute);
            }
        }
        // If all fails, return original (validation will catch)
        return $time;
    }

    private function isValidTime($time)
    {
        return preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time) === 1;
    }
}