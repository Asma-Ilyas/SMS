<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('license_number', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('cnic', 'like', "%{$s}%");
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['name', 'license_number', 'phone', 'is_active', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $drivers = $query->paginate(20)->withQueryString();
        return view('admin.transport.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.transport.drivers.create');
    }

    public function store(StoreDriverRequest $request)
    {
        $data = $request->validated();
        $data = $this->handleUploads($request, $data);

        Driver::create($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver added successfully.');
    }

    public function show(Driver $driver)
    {
        $driver->load('vehicles', 'routes');
        return view('admin.transport.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('admin.transport.drivers.edit', compact('driver'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $data = $request->validated();
        $data = $this->handleUploads($request, $data);

        $driver->update($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        // Soft delete: history (routes/trips referencing this driver) is preserved.
        $driver->delete();
        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted successfully.');
    }

    protected function handleUploads($request, array $data): array
    {
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('drivers/photos', 'public');
        }
        if ($request->hasFile('license_document')) {
            $data['license_document'] = $request->file('license_document')->store('drivers/documents', 'public');
        }
        if ($request->hasFile('cnic_document')) {
            $data['cnic_document'] = $request->file('cnic_document')->store('drivers/documents', 'public');
        }

        return $data;
    }
}
