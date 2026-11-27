<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceBookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Services\Notification\NotificationService;
use App\Services\ServiceBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceBookingController extends Controller
{
    private const SERVICE_TYPES = [
        'regular_service' => 'Servis Berkala',
        'heavy_repair' => 'Perbaikan Mesin & Transmisi',
        'body_repair' => 'Body Repair & Cat Oven',
        'ac_service' => 'Servis AC & Elektrikal',
        'inspection' => 'Inspeksi Lengkap',
    ];

    public function __construct(
        protected NotificationService $notificationService,
        protected ServiceBookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $bookings = ServiceBooking::query()
            ->with(['customer.user', 'vehicle'])
            ->latest()
            ->paginate(15);

        return view('admin.service-bookings.index', compact('bookings'));
    }

    public function show(ServiceBooking $serviceBooking): View
    {
        $serviceBooking->load(['customer.user', 'vehicle', 'workOrder']);
        $mechanics = User::where('role', 'mechanic')->where('is_active', true)->orderBy('name')->get();

        return view('admin.service-bookings.show', compact('serviceBooking', 'mechanics'));
    }

    /** Form booking untuk customer yang datang langsung (walk-in). */
    public function create(): View
    {
        return view('admin.service-bookings.create', [
            'customers' => Customer::with(['user', 'vehicles'])->whereHas('user')->get()->sortBy('user.name'),
            'serviceTypes' => self::SERVICE_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isNewCustomer = ! $request->filled('customer_id');
        $isNewVehicle = $isNewCustomer || ! $request->filled('customer_vehicle_id');

        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'name' => [Rule::requiredIf($isNewCustomer), 'nullable', 'string', 'max:255'],
            'email' => [Rule::requiredIf($isNewCustomer), 'nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => [Rule::requiredIf($isNewCustomer), 'nullable', 'string', 'max:20', Rule::unique('users', 'phone')],
            'customer_vehicle_id' => ['nullable', Rule::exists('customer_vehicles', 'id')->where('customer_id', $request->input('customer_id'))],
            'brand' => [Rule::requiredIf($isNewVehicle), 'nullable', 'string', 'max:50'],
            'model' => [Rule::requiredIf($isNewVehicle), 'nullable', 'string', 'max:50'],
            'year' => [Rule::requiredIf($isNewVehicle), 'nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'plate_number' => ['nullable', 'string', 'max:15'],
            'service_type' => ['required', Rule::in(array_keys(self::SERVICE_TYPES))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking = DB::transaction(function () use ($data, $isNewCustomer, $isNewVehicle) {
            if ($isNewCustomer) {
                // Password acak: customer bisa mengaturnya lewat "Lupa Password"
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'customer',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                $customer = Customer::create(['user_id' => $user->id]);
            } else {
                $customer = Customer::findOrFail($data['customer_id']);
            }

            $vehicle = $isNewVehicle
                ? $customer->vehicles()->create([
                    'brand' => $data['brand'],
                    'model' => $data['model'],
                    'year' => $data['year'],
                    'plate_number' => $data['plate_number'] ?? null,
                ])
                : $customer->vehicles()->findOrFail($data['customer_vehicle_id']);

            return ServiceBooking::create([
                'customer_id' => $customer->id,
                'customer_vehicle_id' => $vehicle->id,
                'service_type' => $data['service_type'],
                'preferred_date' => today(),
                'preferred_time' => now()->format('H:i'),
                'status' => ServiceBookingStatus::Confirmed,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.service-bookings.show', $booking)
            ->with('success', 'Booking walk-in dibuat. Lanjutkan dengan check-in.');
    }

    public function checkIn(Request $request, ServiceBooking $serviceBooking): RedirectResponse
    {
        $data = $request->validate([
            'odometer_km' => ['required', 'integer', 'min:0', 'max:9999999'],
            'body_condition' => ['required', 'string', 'max:2000'],
            'belongings' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->bookingService->checkIn($serviceBooking, $data);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->notificationService->notifyRoles(['mechanic'], 'service_booking_confirmed', [
            'title' => "Mobil Check-in: Booking #{$serviceBooking->id}",
            'message' => 'Kendaraan sudah diverifikasi dan siap dibuatkan Work Order.',
            'action_url' => route('admin.service-bookings.show', $serviceBooking->id),
        ]);

        return back()->with('success', 'Check-in berhasil dicatat.');
    }

    public function updateStatus(Request $request, ServiceBooking $serviceBooking): RedirectResponse
    {
        // Status lain hanya berubah lewat check-in, work order, dan serah terima
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        if ($serviceBooking->workOrder) {
            return back()->with('error', 'Booking yang sudah memiliki Work Order tidak bisa diubah statusnya.');
        }

        $serviceBooking->update(['status' => $request->status]);
        $serviceBooking->load(['customer.user', 'vehicle']);

        try {
            $vehicleName = trim(($serviceBooking->vehicle?->brand ?? '').' '.($serviceBooking->vehicle?->model ?? 'Kendaraan'));
            $bookingDate = $serviceBooking->booking_date ? (is_string($serviceBooking->booking_date) ? $serviceBooking->booking_date : $serviceBooking->booking_date->format('d M Y H:i')) : '-';

            // Kirim notifikasi ke Customer jika ada user-nya
            if ($serviceBooking->customer?->user) {
                $type = $request->status === 'confirmed' ? 'service_booking_confirmed' : 'work_order_status_changed';
                $this->notificationService->notifyCustomer(
                    $serviceBooking->customer->user,
                    $type,
                    [
                        'title' => $request->status === 'confirmed' ? 'Booking Servis Dikonfirmasi' : 'Update Status Booking Servis',
                        'message' => "Status booking servis kendaraan {$vehicleName} telah diperbarui menjadi: ".strtoupper($request->status),
                        'date' => $bookingDate,
                        'vehicle' => $vehicleName,
                        'status' => strtoupper($request->status),
                        'action_url' => route('workshop.booking.index'),
                    ]
                );
            }

            // Notifikasi ke Kepala Mekanik & super-admin
            $this->notificationService->notifyRoles(
                ['mechanic', 'super-admin'],
                'service_booking_confirmed',
                [
                    'title' => 'Status Booking Servis: '.strtoupper($request->status),
                    'message' => "Booking kendaraan {$vehicleName} (".($serviceBooking->customer?->user?->name ?? 'Customer').') status kini: '.strtoupper($request->status),
                    'date' => $bookingDate,
                    'vehicle' => $vehicleName,
                    'status' => strtoupper($request->status),
                    'action_url' => route('admin.service-bookings.show', $serviceBooking->id),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi update booking servis: '.$e->getMessage());
        }

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
