<?php


declare(strict_types=1);


namespace App\Http\Controllers\Customer;


use App\Http\Controllers\Controller;
use App\Models\CustomerVehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $vehicles = $customer->vehicles()->latest()->get();
        return view('customer.vehicles.index', compact('vehicles'));
    }


    public function create(): View
    {
        return view('customer.vehicles.create');
    }


    public function show(Request $request, CustomerVehicle $vehicle): View
    {
        $this->authorize('view', $vehicle);

        return view('customer.vehicles.show', compact('vehicle'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plate_number' => 'nullable|string|max:15',
            'vin' => 'nullable|string|max:17|unique:customer_vehicles,vin',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $customer->vehicles()->create($validated);


        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }


    public function edit(CustomerVehicle $vehicle): View
    {
        $this->authorize('update', $vehicle);
        return view('customer.vehicles.edit', compact('vehicle'));
    }


    public function update(Request $request, CustomerVehicle $vehicle): RedirectResponse
    {
        $this->authorize('update', $vehicle);


        $validated = $request->validate([
            'plate_number' => 'nullable|string|max:15',
            'vin' => 'nullable|string|max:17|unique:customer_vehicles,vin,' . $vehicle->id,
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:500',
        ]);


        $vehicle->update($validated);


        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }


    public function destroy(CustomerVehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete', $vehicle);
        $vehicle->delete();


        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}



