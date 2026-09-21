<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransportRouteRequest;
use App\Http\Requests\UpdateTransportRouteRequest;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = TransportRoute::with('vehicle', 'driver', 'stops');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('start_point', 'like', "%{$s}%")
                  ->orWhere('end_point', 'like', "%{$s}%");
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['name', 'code', 'is_active', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $routes = $query->paginate(20)->withQueryString();
        return view('admin.transport.routes.index', compact('routes'));
    }

    public function create()
    {
        $vehicles = Vehicle::active()->orderBy('vehicle_number')->get();
        $drivers = Driver::active()->orderBy('name')->get();
        return view('admin.transport.routes.create', compact('vehicles', 'drivers'));
    }

    public function store(StoreTransportRouteRequest $request)
    {
        $data = $request->validated();
        $stops = $data['stops'] ?? [];
        unset($data['stops']);

        $route = TransportRoute::create($data);

        foreach ($stops as $i => $stop) {
            $route->stops()->create([
                'stop_name' => $stop['stop_name'],
                'stop_order' => $i + 1,
                'pickup_time' => $stop['pickup_time'] ?? null,
                'drop_time' => $stop['drop_time'] ?? null,
                'latitude' => $stop['latitude'] ?? null,
                'longitude' => $stop['longitude'] ?? null,
            ]);
        }

        return redirect()->route('admin.transport-routes.index')->with('success', 'Route created successfully.');
    }

    public function show(TransportRoute $transportRoute)
    {
        $transportRoute->load('vehicle', 'driver', 'stops', 'studentTransports.student', 'feeTypes');
        return view('admin.transport.routes.show', ['route' => $transportRoute]);
    }

    public function edit(TransportRoute $transportRoute)
    {
        $vehicles = Vehicle::active()->orderBy('vehicle_number')->get();
        $drivers = Driver::active()->orderBy('name')->get();
        $transportRoute->load('stops');
        return view('admin.transport.routes.edit', ['route' => $transportRoute, 'vehicles' => $vehicles, 'drivers' => $drivers]);
    }

    public function update(UpdateTransportRouteRequest $request, TransportRoute $transportRoute)
    {
        $transportRoute->update($request->validated());

        return redirect()->route('admin.transport-routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy(TransportRoute $transportRoute)
    {
        if ($transportRoute->studentTransports()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a route with active student assignments. Reassign or remove those first.');
        }

        $transportRoute->delete();

        return redirect()->route('admin.transport-routes.index')->with('success', 'Route deleted successfully.');
    }

    public function addStop(Request $request, TransportRoute $transportRoute)
    {
        $data = $request->validate([
            'stop_name' => 'required|string',
            'pickup_time' => 'nullable',
            'drop_time' => 'nullable',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data['stop_order'] = $transportRoute->stops()->max('stop_order') + 1;
        $transportRoute->stops()->create($data);

        return back()->with('success', 'Stop added successfully.');
    }

    public function removeStop(TransportRoute $transportRoute, $stopId)
    {
        if ($transportRoute->studentTransports()->where('route_stop_id', $stopId)->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot remove a stop that students are currently assigned to.');
        }

        $transportRoute->stops()->where('id', $stopId)->delete();

        return back()->with('success', 'Stop removed successfully.');
    }
}
