<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('driver');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('vehicle_number', 'like', "%{$s}%")
                  ->orWhere('model', 'like', "%{$s}%")
                  ->orWhere('registration_number', 'like', "%{$s}%")
                  ->orWhereHas('driver', fn ($dq) => $dq->where('name', 'like', "%{$s}%"));
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['vehicle_number', 'type', 'seating_capacity', 'status', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $vehicles = $query->paginate(20)->withQueryString();
        return view('admin.transport.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = Driver::active()->orderBy('name')->get();
        return view('admin.transport.vehicles.create', compact('drivers'));
    }

    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        $data = $this->handleUploads($request, $data);

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('driver', 'routes', 'tripLogs', 'maintenanceLogs');
        return view('admin.transport.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $drivers = Driver::active()->orderBy('name')->get();
        return view('admin.transport.vehicles.edit', compact('vehicle', 'drivers'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();
        $data = $this->handleUploads($request, $data);

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Soft delete: preserves trip/maintenance history and route references.
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }

    public function storeMaintenance(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'maintenance_date' => 'required|date',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date|after_or_equal:maintenance_date',
        ]);

        $vehicle->maintenanceLogs()->create($data);

        return back()->with('success', 'Maintenance record added.');
    }

    protected function handleUploads($request, array $data): array
    {
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('vehicles/photos', 'public');
        }
        if ($request->hasFile('registration_document')) {
            $data['registration_document'] = $request->file('registration_document')->store('vehicles/documents', 'public');
        }
        if ($request->hasFile('insurance_document')) {
            $data['insurance_document'] = $request->file('insurance_document')->store('vehicles/documents', 'public');
        }
        if ($request->hasFile('fitness_document')) {
            $data['fitness_document'] = $request->file('fitness_document')->store('vehicles/documents', 'public');
        }

        return $data;
    }
}
