<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Student;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\AcademicSession;
use App\Models\Grade;
use App\Models\Stream;
use App\Models\ExamMark;
use App\Models\ExamResult;
use App\Models\StudentAttendance;
use App\Models\StudentFeeSubmission;
use App\Models\Invoice;
use App\Models\StudentDiscount;
use App\Models\Discount;
use App\Models\CertificateDistribution;
use App\Models\StudentTransfer;
use App\Models\TransferCertificate;
use App\Models\TimetableEntry;
use App\Models\TimeSlot;
use App\Models\FeeSubmissionType;
use App\Models\TransportRoute;
use App\Models\TransportFeeType;
use App\Models\StudentTransport;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\StudentTransportFeePayment;
use App\Models\StudentHostelFeePayment;
use App\Models\HostelFeeType;
use App\Models\StudentHostelAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classSection.class.grade']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->class_section_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->orderBy('first_name')->paginate(20);
        $classSections = ClassSection::with('class.grade')->get();

        return view('admin.students.index', compact('students', 'classSections'));
    }

    public function create()
    {
        // Get all classes for the dropdown
        $classes = Classes::with(['grade', 'stream'])->get();
        
        // ---- Class sections data for JavaScript ----
        $classSectionsData = [];
        $allClassSections = ClassSection::with(['class'])->get();
        
        foreach ($allClassSections as $section) {
            $classId = $section->class_id;
            if (!isset($classSectionsData[$classId])) {
                $classSectionsData[$classId] = [];
            }
            $classSectionsData[$classId][] = [
                'section_name' => $section->section_name,
                'id' => $section->id,
            ];
        }
        
        // ---- Classes data for JavaScript ----
        $classesData = [];
        foreach ($classes as $class) {
            $classesData[] = [
                'id' => $class->id,
                'grade_name' => $class->grade ? $class->grade->name : null,
                'full_name' => $class->full_name ?? ($class->grade ? $class->grade->name : 'Class'),
            ];
        }
        
        // Get class sections for other dropdowns
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $grades = Grade::all();
        $streams = Stream::all();
        $academicSessions = AcademicSession::where('is_active', true)->get();
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();

        // Transport
        $routes = TransportRoute::with('stops')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $routesData = [];
        foreach ($routes as $route) {
            $stopsData = [];
            foreach ($route->stops as $stop) {
                $stopsData[] = [
                    'id' => $stop->id,
                    'stop_name' => $stop->stop_name
                ];
            }
            $routesData[] = [
                'id' => $route->id,
                'name' => $route->name,
                'code' => $route->code,
                'stops' => $stopsData
            ];
        }
        
        $transportFeeTypes = TransportFeeType::where('is_active', true)->get();

        // Hostel
        $hostels = Hostel::where('is_active', true)->orderBy('name')->get();
        $hostelFeeTypes = HostelFeeType::where('is_active', true)->get();

        return view('admin.students.create', compact(
            'classes',
            'classSections',
            'grades',
            'streams',
            'academicSessions',
            'feeTypes',
            'routes',
            'routesData',
            'transportFeeTypes',
            'hostels',
            'hostelFeeTypes',
            'classSectionsData',
            'classesData'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Personal Details
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'religion' => 'nullable|string|max:255',
            'caste_subcaste' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'extra_note' => 'nullable|string',
            'mother_tongue' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:255',

            // Previous School Details
            'previous_school_name' => 'nullable|string|max:255',
            'previous_school_address' => 'nullable|string|max:255',
            'previous_class' => 'nullable|string|max:255',
            'passout_year' => 'nullable|string|max:10',
            'previous_category' => 'nullable|string|max:255',

            // Admission Details
            'admission_date' => 'required|date',
            'student_type' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'section' => 'required|string|max:10',
            'admission_number' => 'required|unique:students',
            'roll_number' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',

            // Parent Details
            'father_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'mother_occupation' => 'nullable|string|max:255',
            'parent_id_proof' => 'nullable|file|max:2048',
            'parent_signature' => 'nullable|file|max:2048',

            // Concession
            'assigned_concession' => 'nullable|string|max:255',

            // Status & Suspension
            'status' => 'required|in:Active,Inactive',
            'suspension_start_date' => 'nullable|date',
            'suspension_end_date' => 'nullable|date|after_or_equal:suspension_start_date',
            'suspension_message' => 'nullable|string|max:500',

            // Transport (optional)
            'transport_enabled' => 'nullable|boolean',
            'route_id' => 'required_if:transport_enabled,1|nullable|exists:transport_routes,id',
            'route_stop_id' => 'nullable|exists:route_stops,id',
            'transport_fee_type_id' => 'nullable|exists:transport_fee_types,id',

            // Hostel (optional)
            'hostel_enabled' => 'nullable|boolean',
            'hostel_id' => 'required_if:hostel_enabled,1|nullable|exists:hostels,id',
            'room_id' => 'required_if:hostel_enabled,1|nullable|exists:hostel_rooms,id',
            'bed_number' => 'nullable|string|max:20',
            'hostel_fee_type_id' => 'nullable|exists:hostel_fee_types,id',

            // Fee Items (optional)
            'fee_items' => 'nullable|array',
            'fee_items.*.fee_submission_type_id' => 'nullable|exists:fee_submission_types,id',
            'fee_items.*.period' => 'nullable|in:monthly,annually,one_time',
            'fee_items.*.amount' => 'nullable|numeric|min:0',
            'fee_items.*.installment_count' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();

            // Handle class selection - find or create class section
            if ($request->filled('class_id') && $request->filled('section')) {
                $classSection = ClassSection::firstOrCreate(
                    [
                        'class_id' => $request->class_id,
                        'section_name' => $request->section,
                    ],
                    [
                        'capacity' => 30,
                        'student_count' => 0,
                        'student_strength' => 0,
                    ]
                );
                $data['class_section_id'] = $classSection->id;
            }

            // Handle file uploads
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('students/photos', 'public');
                $data['profile_photo'] = $path;
            }

            if ($request->hasFile('parent_id_proof')) {
                $data['parent_id_proof'] = $request->file('parent_id_proof')->store('students/parent_id_proof', 'public');
            }

            if ($request->hasFile('parent_signature')) {
                $data['parent_signature'] = $request->file('parent_signature')->store('students/signatures', 'public');
            }

            // Set default values
            $data['dob_in_words'] = Carbon::parse($request->date_of_birth)->format('F d, Y');
            $data['student_type'] = $request->student_type ?? 'New';
            $data['status'] = $request->status ?? 'Active';

            // Create student
            $student = Student::create($data);

            // Create fee submissions from request or defaults
            $this->createFeeSubmissionsFromRequest($request, $student);

            // Assign transport if enabled
            if ($request->boolean('transport_enabled')) {
                $this->assignTransport($request, $student);
            }

            // Assign hostel if enabled
            if ($request->boolean('hostel_enabled')) {
                $this->assignHostel($request, $student);
            }

            DB::commit();

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $student = Student::with([
            'classSection.class.grade',
            'classSection.class.stream',
            'attendances' => function ($q) {
                $q->orderBy('date', 'desc')->limit(30);
            },
            'examMarks.subject',
            'examResults' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'feeSubmissions.feeSubmissionType',
            'invoices',
            'discounts',
            'certificates',
            'transfers',
            'transports' => function ($q) {
                $q->where('status', '!=', 'inactive')->with(['route', 'routeStop', 'vehicle'])->latest();
            },
            'hostelAllocations' => function ($q) {
                $q->where('status', '!=', 'vacated')->with(['hostel', 'room'])->latest();
            },
        ])->findOrFail($id);

        $stats = $this->calculateStudentStats($student);
        $recentAttendance = $student->attendances()->latest()->take(10)->get();
        $examPerformance = $this->getExamPerformance($student);
        $feeSummary = $this->getFeeSummary($student);
        $currentTransport = $student->transports->first();
        $currentHostelAllocation = $student->hostelAllocations->first();

        // Get timetable for student's class
        $timetable = TimetableEntry::with(['subject', 'teacher', 'room', 'timeSlot', 'classSection'])
            ->where('class_section_id', $student->class_section_id)
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get()
            ->groupBy('day_of_week');

        $timeSlots = TimeSlot::where('is_active', true)
            ->where('type', 'period')
            ->orderBy('sort_order')
            ->get();

        return view('admin.students.show', compact(
            'student',
            'stats',
            'recentAttendance',
            'examPerformance',
            'feeSummary',
            'timetable',
            'timeSlots',
            'currentTransport',
            'currentHostelAllocation'
        ));
    }

    public function edit($id)
    {
        $student = Student::with([
            'transports' => function ($q) {
                $q->where('status', '!=', 'inactive')->latest();
            },
            'hostelAllocations' => function ($q) {
                $q->where('status', '!=', 'vacated')->latest();
            },
        ])->findOrFail($id);

        $classes = Classes::with(['grade', 'stream'])->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $grades = Grade::all();
        $streams = Stream::all();
        $academicSessions = AcademicSession::where('is_active', true)->get();
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();

        $routes = TransportRoute::with('stops')->where('is_active', true)->orderBy('name')->get();
        $transportFeeTypes = TransportFeeType::where('is_active', true)->get();
        $currentTransport = $student->transports->first();

        $hostels = Hostel::where('is_active', true)->orderBy('name')->get();
        $hostelFeeTypes = HostelFeeType::where('is_active', true)->get();
        $currentHostelAllocation = $student->hostelAllocations->first();

        $currentRooms = collect();
        if ($currentHostelAllocation) {
            $currentRooms = HostelRoom::where('hostel_id', $currentHostelAllocation->hostel_id)
                ->where(function ($q) use ($currentHostelAllocation) {
                    $q->where('status', 'available')->orWhere('id', $currentHostelAllocation->room_id);
                })
                ->get();
        }

        return view('admin.students.edit', compact(
            'student',
            'classes',
            'classSections',
            'grades',
            'streams',
            'academicSessions',
            'feeTypes',
            'routes',
            'transportFeeTypes',
            'currentTransport',
            'hostels',
            'hostelFeeTypes',
            'currentHostelAllocation',
            'currentRooms'
        ));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'admission_date' => 'required|date',
            'admission_number' => 'required|unique:students,admission_number,' . $id,
            'class_id' => 'required|exists:classes,id',
            'section' => 'required|string|max:10',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:2048',
            'status' => 'required|in:Active,Inactive',

            'transport_enabled' => 'nullable|boolean',
            'route_id' => 'required_if:transport_enabled,1|nullable|exists:transport_routes,id',
            'route_stop_id' => 'nullable|exists:route_stops,id',
            'transport_fee_type_id' => 'nullable|exists:transport_fee_types,id',

            'hostel_enabled' => 'nullable|boolean',
            'hostel_id' => 'required_if:hostel_enabled,1|nullable|exists:hostels,id',
            'room_id' => 'required_if:hostel_enabled,1|nullable|exists:hostel_rooms,id',
            'bed_number' => 'nullable|string|max:20',
            'hostel_fee_type_id' => 'nullable|exists:hostel_fee_types,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();

            if ($request->filled('class_id') && $request->filled('section')) {
                $classSection = ClassSection::firstOrCreate(
                    [
                        'class_id' => $request->class_id,
                        'section_name' => $request->section,
                    ],
                    [
                        'capacity' => 30,
                        'student_count' => 0,
                        'student_strength' => 0,
                    ]
                );
                $data['class_section_id'] = $classSection->id;
            }

            if ($request->hasFile('profile_photo')) {
                if ($student->profile_photo) {
                    Storage::disk('public')->delete($student->profile_photo);
                }
                $path = $request->file('profile_photo')->store('students/photos', 'public');
                $data['profile_photo'] = $path;
            }

            if ($request->hasFile('parent_id_proof')) {
                if ($student->parent_id_proof) {
                    Storage::disk('public')->delete($student->parent_id_proof);
                }
                $data['parent_id_proof'] = $request->file('parent_id_proof')->store('students/parent_id_proof', 'public');
            }

            if ($request->hasFile('parent_signature')) {
                if ($student->parent_signature) {
                    Storage::disk('public')->delete($student->parent_signature);
                }
                $data['parent_signature'] = $request->file('parent_signature')->store('students/signatures', 'public');
            }

            $data['dob_in_words'] = Carbon::parse($request->date_of_birth)->format('F d, Y');
            $student->update($data);

            $this->syncTransport($request, $student);
            $this->syncHostel($request, $student);

            DB::commit();

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $name = $student->full_name;

            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }

            $student->delete();

            return redirect()->route('admin.students.index')
                ->with('success', "Student {$name} deleted successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function getStudentsByClassSection($classSectionId)
    {
        $students = Student::where('class_section_id', $classSectionId)
                          ->where('status', 'Active')
                          ->orderBy('first_name')
                          ->get(['id', 'first_name', 'last_name', 'admission_number', 'roll_number']);

        return response()->json($students);
    }

    public function promote(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $newClassSectionId = $request->class_section_id;

        if (!$newClassSectionId) {
            return redirect()->back()->with('error', 'Please select a new class section.');
        }

        try {
            $student->update(['class_section_id' => $newClassSectionId]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} promoted successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to promote student: ' . $e->getMessage());
        }
    }

    public function suspend(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'suspension_start_date' => 'required|date',
            'suspension_end_date' => 'required|date|after:suspension_start_date',
            'suspension_message' => 'required|string|max:500',
        ]);

        try {
            $student->update([
                'status' => 'Inactive',
                'suspension_start_date' => $request->suspension_start_date,
                'suspension_end_date' => $request->suspension_end_date,
                'suspension_message' => $request->suspension_message,
            ]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} suspended successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to suspend student: ' . $e->getMessage());
        }
    }

    public function reactivate($id)
    {
        $student = Student::findOrFail($id);

        try {
            $student->update([
                'status' => 'Active',
                'suspension_start_date' => null,
                'suspension_end_date' => null,
                'suspension_message' => null,
            ]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} reactivated successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reactivate student: ' . $e->getMessage());
        }
    }

    /**
     * Show student attendance report
     */
    public function attendanceReport($id)
    {
        $student = Student::with(['classSection'])->findOrFail($id);

        $attendances = StudentAttendance::where('student_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(30);

        $total = $attendances->total();
        $present = StudentAttendance::where('student_id', $id)->where('status', 'present')->count();
        $absent = StudentAttendance::where('student_id', $id)->where('status', 'absent')->count();
        $late = StudentAttendance::where('student_id', $id)->where('status', 'late')->count();
        $halfDay = StudentAttendance::where('student_id', $id)->where('status', 'half_day')->count();
        $leave = StudentAttendance::where('student_id', $id)->where('status', 'leave')->count();

        $attended = $present + $late + $halfDay;
        $percentage = $total > 0 ? round(($attended / $total) * 100, 2) : 0;

        $summary = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'leave' => $leave,
            'percentage' => $percentage,
        ];

        return view('admin.students.attendance', compact('student', 'attendances', 'summary'));
    }

    /**
     * Show student exam results
     */
    public function examResults($id)
    {
        $student = Student::with(['classSection'])->findOrFail($id);

        $examResults = ExamResult::with(['exam', 'exam.examType'])
            ->where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $subjectWise = ExamMark::with(['subject', 'exam'])
            ->where('student_id', $id)
            ->orderBy('subject_id')
            ->get()
            ->groupBy('subject_id');

        return view('admin.students.exam-results', compact('student', 'examResults', 'subjectWise'));
    }

    /**
     * Show student fee details
     */
    public function feeDetails($id)
    {
        $student = Student::findOrFail($id);

        $feeSubmissions = StudentFeeSubmission::with(['feeSubmissionType'])
            ->where('student_id', $id)
            ->orderBy('due_date')
            ->get();

        $invoices = Invoice::with(['bank'])
            ->where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPaid = $feeSubmissions->where('status', 'paid')->sum('paid_amount');
        $totalDue = $feeSubmissions->whereIn('status', ['pending', 'partial'])->sum('amount');
        $totalDiscount = 0;

        return view('admin.students.fee-details', compact(
            'student',
            'feeSubmissions',
            'invoices',
            'totalPaid',
            'totalDue',
            'totalDiscount'
        ));
    }

    /**
     * Create StudentFeeSubmission rows from the create form's "Fee Management"
     * repeatable block.
     */
    private function createFeeSubmissionsFromRequest(Request $request, Student $student)
    {
        $items = collect($request->input('fee_items', []))
            ->filter(fn ($item) => !empty($item['fee_submission_type_id']));

        if ($items->isEmpty()) {
            $this->createDefaultFeeSubmissions($student);
            return;
        }

        foreach ($items as $item) {
            $feeType = FeeSubmissionType::findOrFail($item['fee_submission_type_id']);
            $amount = ($item['amount'] ?? '') !== '' ? (float) $item['amount'] : (float) $feeType->amount;
            $installments = max(1, (int) ($item['installment_count'] ?? 1));
            
            $installmentAmount = round($amount / $installments, 2);
            $totalRounded = $installmentAmount * $installments;
            if ($totalRounded != $amount) {
                $lastInstallmentAmount = $installmentAmount + ($amount - $totalRounded);
            }

            for ($i = 1; $i <= $installments; $i++) {
                $currentAmount = ($i == $installments && isset($lastInstallmentAmount)) 
                    ? $lastInstallmentAmount 
                    : $installmentAmount;

                StudentFeeSubmission::create([
                    'student_id' => $student->id,
                    'fee_submission_type_id' => $feeType->id,
                    'installment_number' => $i,
                    'amount' => $currentAmount,
                    'due_date' => now()->addMonths($i),
                    'status' => 'pending',
                    'paid_amount' => 0,
                ]);
            }
        }
    }

    private function createDefaultFeeSubmissions($student)
    {
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();

        foreach ($feeTypes as $feeType) {
            StudentFeeSubmission::create([
                'student_id' => $student->id,
                'fee_submission_type_id' => $feeType->id,
                'installment_number' => 1,
                'amount' => $feeType->amount,
                'due_date' => now()->addMonths(1),
                'status' => 'pending',
                'paid_amount' => 0,
            ]);
        }
    }

    private function assignTransport(Request $request, Student $student)
    {
        $route = TransportRoute::find($request->route_id);

        $assignment = StudentTransport::create([
            'student_id' => $student->id,
            'route_id' => $request->route_id,
            'route_stop_id' => $request->route_stop_id,
            'vehicle_id' => $route?->vehicle_id,
            'transport_fee_type_id' => $request->transport_fee_type_id,
            'start_date' => now(),
            'status' => 'active',
        ]);

        if ($request->filled('transport_fee_type_id')) {
            $this->createTransportFeePayment($assignment);
        }
    }

    private function createTransportFeePayment(StudentTransport $assignment)
    {
        $feeType = TransportFeeType::find($assignment->transport_fee_type_id);
        if (!$feeType) {
            return;
        }

        StudentTransportFeePayment::create([
            'student_transport_id' => $assignment->id,
            'month' => now()->format('Y-m'),
            'amount' => $feeType->amount,
            'due_date' => now()->addDays(5),
            'status' => 'pending',
        ]);
    }

    private function assignHostel(Request $request, Student $student)
    {
        $room = HostelRoom::findOrFail($request->room_id);

        if ($room->current_occupancy >= $room->capacity) {
            throw new \Exception('Room is full. No vacancy available.');
        }

        $allocation = StudentHostelAllocation::create([
            'student_id' => $student->id,
            'hostel_id' => $request->hostel_id,
            'room_id' => $room->id,
            'hostel_fee_type_id' => $request->hostel_fee_type_id,
            'bed_number' => $request->bed_number,
            'allocation_date' => now(),
            'status' => 'active',
        ]);

        $room->increment('current_occupancy');
        if ($room->current_occupancy >= $room->capacity) {
            $room->update(['status' => 'full']);
        }

        if ($request->filled('hostel_fee_type_id')) {
            $this->createHostelFeePayment($allocation);
        }
    }

    private function createHostelFeePayment(StudentHostelAllocation $allocation)
    {
        $feeType = HostelFeeType::find($allocation->hostel_fee_type_id);
        if (!$feeType) {
            return;
        }

        StudentHostelFeePayment::create([
            'student_hostel_allocation_id' => $allocation->id,
            'month' => now()->format('Y-m'),
            'amount' => $feeType->amount,
            'due_date' => now()->addDays(5),
            'status' => 'pending',
        ]);
    }

    private function syncTransport(Request $request, Student $student)
    {
        $existing = StudentTransport::where('student_id', $student->id)
            ->where('status', '!=', 'inactive')
            ->latest()
            ->first();

        if (!$request->boolean('transport_enabled')) {
            if ($existing) {
                $existing->update(['status' => 'inactive', 'end_date' => now()]);
            }
            return;
        }

        if ($existing) {
            $existing->update([
                'route_id' => $request->route_id,
                'route_stop_id' => $request->route_stop_id,
                'transport_fee_type_id' => $request->transport_fee_type_id,
                'status' => 'active',
            ]);
            $route = TransportRoute::find($request->route_id);
            if ($route) {
                $existing->update(['vehicle_id' => $route->vehicle_id]);
            }
        } else {
            $this->assignTransport($request, $student);
        }
    }

    private function syncHostel(Request $request, Student $student)
    {
        $existing = StudentHostelAllocation::where('student_id', $student->id)
            ->where('status', '!=', 'vacated')
            ->latest()
            ->first();

        if (!$request->boolean('hostel_enabled')) {
            if ($existing) {
                $this->releaseRoom($existing->room_id);
                $existing->update(['status' => 'vacated', 'vacate_date' => now()]);
            }
            return;
        }

        if ($existing && (int) $existing->room_id === (int) $request->room_id) {
            $existing->update([
                'hostel_id' => $request->hostel_id,
                'hostel_fee_type_id' => $request->hostel_fee_type_id,
                'bed_number' => $request->bed_number,
                'status' => 'active',
            ]);
            return;
        }

        if ($existing) {
            $this->releaseRoom($existing->room_id);
            $existing->update(['status' => 'vacated', 'vacate_date' => now()]);
        }

        $this->assignHostel($request, $student);
    }

    private function releaseRoom($roomId)
    {
        $room = HostelRoom::find($roomId);
        if (!$room) {
            return;
        }

        $room->decrement('current_occupancy');
        if ($room->status === 'full' && $room->current_occupancy < $room->capacity) {
            $room->update(['status' => 'available']);
        }
    }

    private function calculateStudentStats($student)
    {
        $totalDays = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $absentDays = $student->attendances()->where('status', 'absent')->count();
        $lateDays = $student->attendances()->where('status', 'late')->count();
        $halfDayDays = $student->attendances()->where('status', 'half_day')->count();
        $leaveDays = $student->attendances()->where('status', 'leave')->count();

        $attended = $presentDays + $lateDays + $halfDayDays;
        $attendancePercentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 2) : 0;

        $totalExams = $student->examResults()->count();
        $avgPercentage = $student->examResults()->avg('percentage') ?? 0;
        $grade = $this->calculateGrade($avgPercentage);

        $feeSubmissions = $student->feeSubmissions;
        $totalFee = $feeSubmissions->sum('amount');
        $paidFee = $feeSubmissions->where('status', 'paid')->sum('paid_amount');
        $pendingFee = $totalFee - $paidFee;

        return [
            'total_attendance_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'half_day_days' => $halfDayDays,
            'leave_days' => $leaveDays,
            'attendance_percentage' => $attendancePercentage,
            'total_exams' => $totalExams,
            'avg_percentage' => $avgPercentage,
            'grade' => $grade,
            'total_fee' => $totalFee,
            'paid_fee' => $paidFee,
            'pending_fee' => $pendingFee,
        ];
    }

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

    private function getExamPerformance($student)
    {
        return ExamResult::with(['exam'])
                         ->where('student_id', $student->id)
                         ->orderBy('created_at', 'desc')
                         ->take(5)
                         ->get();
    }

    private function getFeeSummary($student)
    {
        $feeSubmissions = $student->feeSubmissions;
        return [
            'total' => $feeSubmissions->sum('amount'),
            'paid' => $feeSubmissions->where('status', 'paid')->sum('paid_amount'),
            'pending' => $feeSubmissions->whereIn('status', ['pending', 'partial'])->sum('amount'),
            'overdue' => $feeSubmissions->where('status', 'pending')->where('due_date', '<', now())->count(),
            'total_installments' => $feeSubmissions->count(),
            'paid_installments' => $feeSubmissions->where('status', 'paid')->count(),
            'pending_installments' => $feeSubmissions->where('status', 'pending')->count(),
            'partial_installments' => $feeSubmissions->where('status', 'partial')->count(),
        ];
    }
}