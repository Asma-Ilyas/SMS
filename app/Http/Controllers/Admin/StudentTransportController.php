<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentTransportRequest;
use App\Http\Requests\UpdateStudentTransportRequest;
use App\Models\StudentTransport;
use App\Models\Student;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class StudentTransportController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentTransport::with('student', 'route', 'stop', 'vehicle');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('student', fn ($sq) => $sq->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%")->orWhere('admission_number', 'like', "%{$s}%"))
                  ->orWhereHas('route', fn ($rq) => $rq->where('name', 'like', "%{$s}%"));
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['status', 'start_date', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $assignments = $query->paginate(20)->withQueryString();
        return view('admin.transport.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $routes = TransportRoute::active()->with('stops')->orderBy('name')->get();
        return view('admin.transport.assignments.create', compact('students', 'routes'));
    }

    public function store(StoreStudentTransportRequest $request)
    {
        $data = $request->validated();

        $alreadyAssigned = StudentTransport::where('student_id', $data['student_id'])
            ->where('status', 'active')
            ->exists();

        if ($alreadyAssigned) {
            return back()->withInput()->with('error', 'This student already has an active transport assignment. Deactivate it first before reassigning.');
        }

        $route = TransportRoute::findOrFail($data['route_id']);
        $data['vehicle_id'] = $route->vehicle_id;
        $data['status'] = 'active';

        StudentTransport::create($data);

        return redirect()->route('admin.student-transports.index')->with('success', 'Student assigned to route successfully.');
    }

    public function show(StudentTransport $studentTransport)
    {
        $studentTransport->load('student', 'route', 'stop', 'vehicle', 'feeType', 'feePayments');
        return view('admin.transport.assignments.show', ['assignment' => $studentTransport]);
    }

    public function edit(StudentTransport $studentTransport)
    {
        $students = Student::orderBy('first_name')->get();
        $routes = TransportRoute::active()->with('stops')->orderBy('name')->get();
        return view('admin.transport.assignments.edit', ['assignment' => $studentTransport, 'students' => $students, 'routes' => $routes]);
    }

    public function update(UpdateStudentTransportRequest $request, StudentTransport $studentTransport)
    {
        $data = $request->validated();

        $route = TransportRoute::findOrFail($data['route_id']);
        $data['vehicle_id'] = $route->vehicle_id;

        $studentTransport->update($data);

        return redirect()->route('admin.student-transports.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(StudentTransport $studentTransport)
    {
        $studentTransport->delete();
        return redirect()->route('admin.student-transports.index')->with('success', 'Assignment removed successfully.');
    }

    public function getStopsByRoute($routeId)
    {
        $stops = TransportRoute::findOrFail($routeId)->stops;
        return response()->json($stops);
    }
}
