<?php


declare(strict_types=1);


namespace App\Http\Controllers\Workshop;


use App\Http\Controllers\Controller;
use App\Http\Requests\Workshop\StoreServiceBookingRequest;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Services\ServiceBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class WorkshopController extends Controller
{
    public function __construct(
        protected ServiceBookingService $bookingService
    ) {}


    public function index(Request $request): View
    {
        $customer = $request->user()->customer;


        $bookings = ServiceBooking::where('customer_id', $customer->id)
            ->with('vehicle')
            ->latest()
            ->paginate(10);


        return view('workshop.index', compact('bookings'));
    }


    public function create(Request $request): View
    {
        $customer = $request->user()->customer;
        $vehicles = CustomerVehicle::where('customer_id', $customer->id)->get();


        return view('workshop.create', compact('vehicles'));
    }


    public function store(StoreServiceBookingRequest $request): RedirectResponse
    {
        $customer = $request->user()->customer;
        $vehicle = CustomerVehicle::where('id', $request->customer_vehicle_id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();


        try {
            $booking = $this->bookingService->create(
                $customer,
                $vehicle,
                $request->service_type,
                $request->preferred_date,
                $request->preferred_time,
                $request->notes
            );


            return redirect()
                ->route('workshop.show', $booking)
                ->with('success', 'Booking servis berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    public function show(Request $request, ServiceBooking $booking): View
    {
        $customer = $request->user()->customer;


        // Pastikan booking milik customer
        if ($booking->customer_id !== $customer->id) {
            abort(403);
        }


        $booking->load(['vehicle', 'workOrder.items']);


        return view('workshop.show', compact('booking'));
    }


    public function cancel(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $customer = $request->user()->customer;


        if ($booking->customer_id !== $customer->id) {
            abort(403);
        }


        try {
            $this->bookingService->cancel($booking);


            return redirect()
                ->route('workshop.index')
                ->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function history(Request $request): View
    {
        $customer = $request->user()->customer;


        $bookings = ServiceBooking::where('customer_id', $customer->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with('vehicle')
            ->latest()
            ->paginate(10);


        return view('workshop.history', compact('bookings'));
    }
}



