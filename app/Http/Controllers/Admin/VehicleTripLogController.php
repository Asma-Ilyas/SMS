<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleTripLog;
use App\Models\Vehicle;
use App\Models\TransportRoute;
use App\Models\Driver;
use Illuminate\Http\Request;

class VehicleTripLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleTripLog::with('vehicle', 'route', 'driver')->latest('trip_date');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('trip_date', $request->date);
        }

        $trips = $query->paginate(25);
        $vehicles = Vehicle::orderBy('vehicle_number')->get();

        return view('admin.transport.trips.index', compact('trips', 'vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::active()->orderBy('vehicle_number')->get();
        $routes = TransportRoute::active()->orderBy('name')->get();
        $drivers = Driver::active()->orderBy('name')->get();
        return view('admin.transport.trips.create', compact('vehicles', 'routes', 'drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'route_id' => 'nullable|exists:transport_routes,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'trip_date' => 'required|date',
            'trip_type' => 'required|in:pickup,drop',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        VehicleTripLog::create($data);

        return redirect()->route('admin.vehicle-trips.index')->with('success', 'Trip logged successfully.');
    }

    public function updateStatus(Request $request, VehicleTripLog $vehicleTrip)
    {
        $request->validate(['status' => 'required|in:scheduled,in_progress,completed,cancelled']);
        $vehicleTrip->update(['status' => $request->status]);
        return back()->with('success', 'Trip status updated.');
    }

    public function destroy(VehicleTripLog $vehicleTrip)
    {
        $vehicleTrip->delete();
        return back()->with('success', 'Trip log deleted.');
    }
}
