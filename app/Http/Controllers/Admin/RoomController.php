<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Block;
use App\Models\Floor;
use App\Models\SchoolTiming;
use App\Models\SectionRoomAssignment;
use App\Models\ClassSection;
use App\Models\Student;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index(Request $request)
    {
        $query = Room::query();
        
        // Get column names to avoid errors
        $columns = Schema::getColumnListing('rooms');
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $columns) {
                if (in_array('name', $columns)) {
                    $q->orWhere('name', 'like', '%' . $search . '%');
                }
                if (in_array('room_number', $columns)) {
                    $q->orWhere('room_number', 'like', '%' . $search . '%');
                }
                if (in_array('type', $columns)) {
                    $q->orWhere('type', 'like', '%' . $search . '%');
                }
            });
        }
        
        // Apply block filter
        if ($request->filled('block_id')) {
            if (in_array('block_id', $columns)) {
                $query->where('block_id', $request->block_id);
            } elseif (in_array('block', $columns)) {
                $query->where('block', $request->block_id);
            }
        }
        
        // Apply floor filter
        if ($request->filled('floor_id')) {
            if (in_array('floor_id', $columns)) {
                $query->where('floor_id', $request->floor_id);
            } elseif (in_array('floor', $columns)) {
                $query->where('floor', $request->floor_id);
            }
        }
        
        // Apply availability filter
        if ($request->filled('available')) {
            if (in_array('is_available', $columns)) {
                $query->where('is_available', true);
            }
        }
        
        // Apply ordering based on available columns
        if (in_array('block', $columns)) {
            $query->orderBy('block');
        } elseif (in_array('block_id', $columns)) {
            $query->orderBy('block_id');
        }
        
        if (in_array('floor', $columns)) {
            $query->orderBy('floor');
        } elseif (in_array('floor_id', $columns)) {
            $query->orderBy('floor_id');
        }
        
        if (in_array('room_number', $columns)) {
            $query->orderBy('room_number');
        } elseif (in_array('name', $columns)) {
            $query->orderBy('name');
        }
        
        // If no ordering applied, order by id
        if (empty($query->getQuery()->orders)) {
            $query->orderBy('id');
        }
        
        $rooms = $query->paginate(20)->withQueryString();
        
        // Get stats
        $totalRooms = Room::count();
        $availableRooms = Room::where('is_available', true)->count();
        $occupiedRooms = Room::where('is_available', false)->count();
        
        $session = SchoolTiming::where('is_active', true)->first();
        $assignedCount = 0;
        $overCapacity = 0;
        
        if ($session) {
            try {
                if (Schema::hasTable('section_room_assignments')) {
                    $assignedCount = SectionRoomAssignment::where('school_timing_id', $session->id)->count();
                    $overCapacity = SectionRoomAssignment::where('school_timing_id', $session->id)
                                    ->where('fit_status', 'over_capacity')
                                    ->count();
                }
            } catch (\Exception $e) {
                $assignedCount = 0;
                $overCapacity = 0;
            }
        }
        
        // Create stats array
        $stats = [
            'totalRooms' => $totalRooms,
            'availableRooms' => $availableRooms,
            'occupiedRooms' => $occupiedRooms,
            'assignedCount' => $assignedCount,
            'overCapacity' => $overCapacity,
        ];
        
        // Get blocks and floors for filters
        $blocks = collect();
        $floors = collect();
        
        try {
            if (class_exists(Block::class) && Schema::hasTable('blocks')) {
                $blocks = Block::orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // Block model doesn't exist or table doesn't exist
        }
        
        try {
            if (class_exists(Floor::class) && Schema::hasTable('floors')) {
                $floors = Floor::orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // Floor model doesn't exist or table doesn't exist
        }
        
        return view('admin.rooms.index', compact(
            'rooms',
            'stats',
            'blocks',
            'floors',
            'session'
        ));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        $blocks = collect();
        $floors = collect();
        
        try {
            if (class_exists(Block::class) && Schema::hasTable('blocks')) {
                $blocks = Block::orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // Block model doesn't exist
        }
        
        try {
            if (class_exists(Floor::class) && Schema::hasTable('floors')) {
                $floors = Floor::orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // Floor model doesn't exist
        }
        
        return view('admin.rooms.create', compact('blocks', 'floors'));
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $columns = Schema::getColumnListing('rooms');
        $rules = [
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ];
        
        if (in_array('room_number', $columns)) {
            $rules['room_number'] = 'nullable|string|max:50';
        }
        if (in_array('type', $columns)) {
            $rules['type'] = 'nullable|string|max:100';
        }
        if (in_array('block_id', $columns)) {
            $rules['block_id'] = 'nullable|exists:blocks,id';
        }
        if (in_array('block', $columns)) {
            $rules['block'] = 'nullable|string|max:100';
        }
        if (in_array('floor_id', $columns)) {
            $rules['floor_id'] = 'nullable|exists:floors,id';
        }
        if (in_array('floor', $columns)) {
            $rules['floor'] = 'nullable|string|max:100';
        }
        if (in_array('is_available', $columns)) {
            $rules['is_available'] = 'nullable|boolean';
        }
        
        $request->validate($rules);

        try {
            $data = [
                'name' => $request->name,
                'capacity' => $request->capacity,
            ];
            
            if (in_array('room_number', $columns)) {
                $data['room_number'] = $request->room_number;
            }
            if (in_array('type', $columns)) {
                $data['type'] = $request->type;
            }
            if (in_array('block_id', $columns)) {
                $data['block_id'] = $request->block_id;
            }
            if (in_array('block', $columns)) {
                $data['block'] = $request->block;
            }
            if (in_array('floor_id', $columns)) {
                $data['floor_id'] = $request->floor_id;
            }
            if (in_array('floor', $columns)) {
                $data['floor'] = $request->floor;
            }
            if (in_array('is_available', $columns)) {
                $data['is_available'] = $request->has('is_available') ? true : false;
            }
            
            $room = Room::create($data);

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room "' . $room->name . '" created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating room: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create room: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        $assignments = collect();
        try {
            if (Schema::hasTable('section_room_assignments')) {
                $assignments = SectionRoomAssignment::with(['section', 'section.class.grade', 'room', 'schoolTiming'])
                    ->where('room_id', $room->id)
                    ->get();
            }
        } catch (\Exception $e) {
            // SectionRoomAssignment table doesn't exist
        }
            
        return view('admin.rooms.show', compact('room', 'assignments'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room)
    {
        $blocks = collect();
        $floors = collect();
        
        try {
            if (class_exists(Block::class) && Schema::hasTable('blocks')) {
                $blocks = Block::orderBy('name')->get();
            }
        } catch (\Exception $e) {}
        
        try {
            if (class_exists(Floor::class) && Schema::hasTable('floors')) {
                $floors = Floor::orderBy('name')->get();
            }
        } catch (\Exception $e) {}
        
        return view('admin.rooms.edit', compact('room', 'blocks', 'floors'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        $columns = Schema::getColumnListing('rooms');
        $rules = [
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ];
        
        if (in_array('room_number', $columns)) {
            $rules['room_number'] = 'nullable|string|max:50';
        }
        if (in_array('type', $columns)) {
            $rules['type'] = 'nullable|string|max:100';
        }
        if (in_array('block_id', $columns)) {
            $rules['block_id'] = 'nullable|exists:blocks,id';
        }
        if (in_array('block', $columns)) {
            $rules['block'] = 'nullable|string|max:100';
        }
        if (in_array('floor_id', $columns)) {
            $rules['floor_id'] = 'nullable|exists:floors,id';
        }
        if (in_array('floor', $columns)) {
            $rules['floor'] = 'nullable|string|max:100';
        }
        if (in_array('is_available', $columns)) {
            $rules['is_available'] = 'nullable|boolean';
        }
        
        $request->validate($rules);

        try {
            $data = [
                'name' => $request->name,
                'capacity' => $request->capacity,
            ];
            
            if (in_array('room_number', $columns)) {
                $data['room_number'] = $request->room_number;
            }
            if (in_array('type', $columns)) {
                $data['type'] = $request->type;
            }
            if (in_array('block_id', $columns)) {
                $data['block_id'] = $request->block_id;
            }
            if (in_array('block', $columns)) {
                $data['block'] = $request->block;
            }
            if (in_array('floor_id', $columns)) {
                $data['floor_id'] = $request->floor_id;
            }
            if (in_array('floor', $columns)) {
                $data['floor'] = $request->floor;
            }
            if (in_array('is_available', $columns)) {
                $data['is_available'] = $request->has('is_available') ? true : false;
            }
            
            $room->update($data);

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room "' . $room->name . '" updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating room: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update room: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
    {
        try {
            // Check if room has assignments
            $assignments = 0;
            try {
                if (Schema::hasTable('section_room_assignments')) {
                    $assignments = SectionRoomAssignment::where('room_id', $room->id)->count();
                }
            } catch (\Exception $e) {}
            
            if ($assignments > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete room. It has ' . $assignments . ' active assignments.');
            }
            
            $room->delete();

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting room: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete room: ' . $e->getMessage());
        }
    }

    /**
     * Toggle room availability.
     */
    public function toggleAvailability(Room $room)
    {
        try {
            $room->update([
                'is_available' => !$room->is_available
            ]);

            $status = $room->is_available ? 'available' : 'unavailable';
            
            return redirect()->back()
                ->with('success', 'Room "' . $room->name . '" is now ' . $status . '.');

        } catch (\Exception $e) {
            Log::error('Error toggling room availability: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update room availability.');
        }
    }

    /**
     * Display room assignments page.
     */
    public function assignments(Request $request)
    {
        // Get session
        $session = SchoolTiming::where('is_active', true)->first();
        
        if (!$session) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'No active timing session found. Please activate a session first.');
        }
        
        // Get all timings for dropdown
        $allTimings = SchoolTiming::orderBy('is_active', 'desc')->get();
        
        // Get all rooms
        $rooms = Room::where('is_available', true)->orderBy('name')->get();
        
        // Get assignments
        $assignments = collect();
        $assignedSectionIds = [];
        
        try {
            if (Schema::hasTable('section_room_assignments')) {
                $assignments = SectionRoomAssignment::with(['section', 'section.class.grade', 'room'])
                    ->where('school_timing_id', $session->id)
                    ->get();
                    
                $assignedSectionIds = $assignments->pluck('section_id')->toArray();
            }
        } catch (\Exception $e) {
            // Table doesn't exist
        }
        
        // Get unassigned sections
        $unassigned = ClassSection::with(['class.grade'])
            ->whereNotIn('id', $assignedSectionIds)
            ->get();
        
        // Get free rooms (rooms not assigned to any section)
        $assignedRoomIds = $assignments->pluck('room_id')->toArray();
        $freeRooms = Room::whereNotIn('id', $assignedRoomIds)
            ->where('is_available', true)
            ->orderBy('name')
            ->get();
        
        // Calculate stats
        $stats = [
            'totalRooms' => Room::count(),
            'freeRooms' => $freeRooms->count(),
            'assignedCount' => $assignments->count(),
            'overCapacity' => $assignments->where('fit_status', 'over_capacity')->count(),
        ];
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $unassigned = $unassigned->filter(function($section) use ($search) {
                return stripos($section->section_name, $search) !== false || 
                       stripos($section->class->grade->name ?? '', $search) !== false;
            });
        }
        
        if ($request->filled('fit_status')) {
            $assignments = $assignments->filter(function($assignment) use ($request) {
                return $assignment->fit_status === $request->fit_status;
            });
        }
        
        return view('admin.rooms.assignments', compact(
            'session',
            'allTimings',
            'rooms',
            'assignments',
            'unassigned',
            'freeRooms',
            'stats'
        ));
    }

    /**
     * Auto-assign rooms to all unassigned sections.
     */
    public function autoAssign(Request $request)
    {
        $request->validate([
            'timing_id' => 'required|exists:school_timings,id',
        ]);

        $session = SchoolTiming::findOrFail($request->timing_id);
        
        try {
            DB::beginTransaction();
            
            $sections = ClassSection::withCount('students')->get();
            $rooms = Room::where('is_available', true)->orderBy('capacity')->get();
            
            $existingAssignments = SectionRoomAssignment::where('school_timing_id', $session->id)
                ->pluck('section_id')
                ->toArray();
            
            $results = [
                'total_sections' => 0,
                'assigned' => 0,
                'over_capacity' => [],
                'no_room' => [],
                'skipped_manual' => 0,
                'rooms_free' => 0,
            ];
            
            $roomUsage = [];
            foreach ($rooms as $room) {
                $roomUsage[$room->id] = 0;
            }
            
            foreach ($sections as $section) {
                if (in_array($section->id, $existingAssignments)) {
                    $results['skipped_manual']++;
                    continue;
                }
                
                $results['total_sections']++;
                $strength = $section->students_count ?? 0;
                $selectedRoom = null;
                
                foreach ($rooms as $room) {
                    if ($room->capacity >= $strength && $roomUsage[$room->id] < 1) {
                        $selectedRoom = $room;
                        break;
                    }
                }
                
                if ($selectedRoom) {
                    $fitStatus = 'perfect';
                    if ($strength > $selectedRoom->capacity * 0.9) {
                        $fitStatus = 'tight';
                    }
                    
                    SectionRoomAssignment::create([
                        'section_id' => $section->id,
                        'room_id' => $selectedRoom->id,
                        'school_timing_id' => $session->id,
                        'assignment_type' => 'auto',
                        'section_strength' => $strength,
                        'room_capacity' => $selectedRoom->capacity,
                        'fit_status' => $fitStatus,
                        'suggested_room_id' => null,
                        'notes' => 'Auto-assigned',
                    ]);
                    
                    $roomUsage[$selectedRoom->id]++;
                    $results['assigned']++;
                } else {
                    $results['no_room'][] = [
                        'section' => $section->section_name,
                        'strength' => $strength,
                    ];
                }
            }
            
            $results['rooms_free'] = collect($roomUsage)->filter(function($usage) {
                return $usage === 0;
            })->count();
            
            DB::commit();
            
            return redirect()->route('admin.rooms.assignments', ['timing_id' => $session->id])
                ->with('success', 'Auto-assignment completed!')
                ->with('assignment_results', [
                    'summary' => $results,
                    'over_capacity' => $results['over_capacity'],
                    'no_room' => $results['no_room'],
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-assign error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to auto-assign: ' . $e->getMessage());
        }
    }

    /**
     * Manually assign a section to a room.
     */
    public function manualAssign(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:class_sections,id',
            'room_id' => 'required|exists:rooms,id',
            'school_timing_id' => 'required|exists:school_timings,id',
        ]);

        try {
            $existing = SectionRoomAssignment::where('section_id', $request->section_id)
                ->where('school_timing_id', $request->school_timing_id)
                ->first();
            
            if ($existing) {
                return redirect()->back()
                    ->with('error', 'This section already has an assignment.');
            }
            
            $room = Room::findOrFail($request->room_id);
            $section = ClassSection::findOrFail($request->section_id);
            $strength = $section->students_count ?? 0;
            
            $fitStatus = 'perfect';
            if ($strength > $room->capacity) {
                $fitStatus = 'over_capacity';
            } elseif ($strength > $room->capacity * 0.9) {
                $fitStatus = 'tight';
            }
            
            SectionRoomAssignment::create([
                'section_id' => $request->section_id,
                'room_id' => $request->room_id,
                'school_timing_id' => $request->school_timing_id,
                'assignment_type' => 'manual',
                'section_strength' => $strength,
                'room_capacity' => $room->capacity,
                'fit_status' => $fitStatus,
                'suggested_room_id' => null,
                'notes' => 'Manually assigned',
            ]);

            return redirect()->back()
                ->with('success', 'Section assigned to room successfully!');

        } catch (\Exception $e) {
            Log::error('Manual assign error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to assign: ' . $e->getMessage());
        }
    }

    /**
     * Adjust assignment for a section (find better room).
     */
    public function adjustAssignment(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:class_sections,id',
            'school_timing_id' => 'required|exists:school_timings,id',
        ]);

        try {
            $section = ClassSection::findOrFail($request->section_id);
            $strength = $section->students_count ?? 0;
            
            $assignment = SectionRoomAssignment::where('section_id', $request->section_id)
                ->where('school_timing_id', $request->school_timing_id)
                ->first();
            
            if (!$assignment) {
                return redirect()->back()
                    ->with('error', 'No assignment found for this section.');
            }
            
            $betterRoom = Room::where('is_available', true)
                ->where('capacity', '>=', $strength)
                ->where('id', '!=', $assignment->room_id)
                ->whereNotIn('id', function($query) use ($request) {
                    $query->select('room_id')
                        ->from('section_room_assignments')
                        ->where('school_timing_id', $request->school_timing_id);
                })
                ->orderBy('capacity')
                ->first();
            
            if ($betterRoom) {
                $assignment->update([
                    'room_id' => $betterRoom->id,
                    'room_capacity' => $betterRoom->capacity,
                    'fit_status' => $strength > $betterRoom->capacity * 0.9 ? 'tight' : 'perfect',
                    'notes' => 'Re-adjusted to better fit',
                ]);
                
                return redirect()->back()
                    ->with('success', 'Assignment adjusted to a better room!');
            } else {
                return redirect()->back()
                    ->with('warning', 'No better room found for this section.');
            }

        } catch (\Exception $e) {
            Log::error('Adjust assignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to adjust assignment: ' . $e->getMessage());
        }
    }

    /**
     * Remove an assignment.
     */
    public function removeAssignment($id)
    {
        try {
            $assignment = SectionRoomAssignment::findOrFail($id);
            $assignment->delete();

            return redirect()->back()
                ->with('success', 'Assignment removed successfully.');

        } catch (\Exception $e) {
            Log::error('Remove assignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to remove assignment: ' . $e->getMessage());
        }
    }
}