<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workshop\StoreServiceBookingRequest;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Services\ServiceBookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceBookingController extends Controller
{
    public function __construct(
        protected ServiceBookingService $bookingService
    ) {}

    /**
     * Halaman publik info workshop & opsi booking servis.
     */
    public function publicIndex(Request $request): View
    {
        $services = [
            [
                'id' => 'regular_service',
                'name' => 'Servis Berkala',
                'description' => 'Ganti oli, filter oli, tune-up mesin, cek 25 titik keselamatan kendaraan.',
                'price' => 'Mulai Rp 750.000',
                'duration' => '± 2 Jam',
                'icon' => 'wrench-screwdriver',
            ],
            [
                'id' => 'heavy_repair',
                'name' => 'Perbaikan Mesin & Transmisi',
                'description' => 'Overhaul mesin, perbaikan gearbox matic/manual, kalibrasi sistem pembakaran.',
                'price' => 'Estimasi setelah inspeksi',
                'duration' => '1 - 3 Hari',
                'icon' => 'cog',
            ],
            [
                'id' => 'body_repair',
                'name' => 'Body Repair & Cat Oven',
                'description' => 'Perbaikan bodi penyok, cat oven teknologi DuPont, poles detail nano-ceramic.',
                'price' => 'Mulai Rp 1.200.000 / panel',
                'duration' => '2 - 5 Hari',
                'icon' => 'sparkles',
            ],
            [
                'id' => 'ac_service',
                'name' => 'Servis AC & Elektrikal',
                'description' => 'Flushing freon, ganti evaporator, cek sensor ECU & kelistrikan mobil.',
                'price' => 'Mulai Rp 500.000',
                'duration' => '± 2 Jam',
                'icon' => 'bolt',
            ],
            [
                'id' => 'inspection',
                'name' => 'Inspeksi Lengkap (150+ Titik)',
                'description' => 'Pemeriksaan komprehensif struktur, mesin, komputer OBD-II & test drive.',
                'price' => 'Mulai Rp 600.000',
                'duration' => '± 1.5 Jam',
                'icon' => 'clipboard-document-check',
            ],
        ];

        return view('workshop.index', compact('services'));
    }

    /**
     * Tampilkan formulir booking servis.
     */
    public function create(Request $request): View
    {
        $customer = $request->user()?->customer;
        $vehicles = $customer ? CustomerVehicle::where('customer_id', $customer->id)->get() : collect();

        return view('workshop.create', compact('vehicles'));
    }

    /**
     * Simpan booking servis baru.
     */
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
                Carbon::parse($request->preferred_date),
                $request->preferred_time,
                $request->notes
            );

            return redirect()
                ->route('workshop.booking.show', $booking)
                ->with('success', 'Booking servis berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar booking milik customer yang sedang login.
     */
    public function myBookings(Request $request): View
    {
        $customer = $request->user()->customer;

        $bookings = ServiceBooking::where('customer_id', $customer->id)
            ->with(['vehicle', 'workOrder'])
            ->latest()
            ->paginate(10);

        return view('workshop.index', compact('bookings'));
    }

    /**
     * Tampilkan detail booking servis.
     */
    public function show(Request $request, ServiceBooking $booking): View
    {
        $customer = $request->user()->customer;

        if ($booking->customer_id !== $customer->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $booking->load(['vehicle', 'workOrder.items']);

        return view('workshop.show', compact('booking'));
    }

    /**
     * Tampilkan bukti booking / kwitansi.
     */
    public function receipt(Request $request, ServiceBooking $booking): View
    {
        $customer = $request->user()->customer;

        if ($booking->customer_id !== $customer->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $booking->load(['vehicle', 'workOrder.items']);

        return view('workshop.show', compact('booking'));
    }

    /**
     * Riwayat servis yang sudah selesai atau dibatalkan.
     */
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

    /**
     * Batalkan booking servis.
     */
    public function cancel(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $customer = $request->user()->customer;

        if ($booking->customer_id !== $customer->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        try {
            $this->bookingService->cancel($booking);

            return redirect()
                ->route('workshop.booking.index')
                ->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
