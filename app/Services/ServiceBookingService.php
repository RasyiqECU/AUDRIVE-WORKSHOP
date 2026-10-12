<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\ServiceBookingStatus;
use App\Enums\WorkOrderStatus;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ServiceBookingService
{
    public function create(
        Customer $customer,
        CustomerVehicle $vehicle,
        string $serviceType,
        Carbon $preferredDate,
        string $preferredTime,
        ?string $notes = null
    ): ServiceBooking {
        // Validasi: kendaraan harus milik customer
        if ($vehicle->customer_id !== $customer->id) {
            throw new \InvalidArgumentException('Kendaraan tidak terdaftar atas nama Anda.');
        }


        // Validasi: tanggal tidak boleh di masa lalu
        if ($preferredDate->isPast()) {
            throw new \InvalidArgumentException('Tanggal booking tidak boleh di masa lalu.');
        }


        // Hitung estimasi biaya berdasarkan tipe servis
        $estimatedCost = $this->calculateEstimatedCost($serviceType);


        return ServiceBooking::create([
            'customer_id' => $customer->id,
            'customer_vehicle_id' => $vehicle->id,
            'service_type' => $serviceType,
            'preferred_date' => $preferredDate,
            'preferred_time' => $preferredTime,
            'status' => ServiceBookingStatus::Pending,
            'estimated_cost_idr' => $estimatedCost,
            'notes' => $notes,
        ]);
    }


    public function confirm(ServiceBooking $booking): ServiceBooking
    {
        $statusVal = $booking->status instanceof ServiceBookingStatus ? $booking->status->value : (string) $booking->status;
        if ($statusVal !== 'pending') {
            throw new \InvalidArgumentException('Hanya booking pending yang bisa dikonfirmasi.');
        }

        $booking->update(['status' => ServiceBookingStatus::Confirmed]);
        return $booking->fresh();
    }

    public function cancel(ServiceBooking $booking): ServiceBooking
    {
        $statusVal = $booking->status instanceof ServiceBookingStatus ? $booking->status->value : (string) $booking->status;
        if (in_array($statusVal, ['completed', 'cancelled'], true)) {
            throw new \InvalidArgumentException('Booking tidak bisa dibatalkan.');
        }

        $booking->update(['status' => ServiceBookingStatus::Cancelled]);
        return $booking->fresh();
    }

    public function convertToWorkOrder(ServiceBooking $booking, ?int $mechanicId = null): WorkOrder
    {
        // Pastikan relasi workOrder sudah dimuat
        $booking->loadMissing('workOrder');

        // Jika sudah ada work order, langsung kembalikan
        if ($booking->workOrder) {
            return $booking->workOrder;
        }

        // Ambil nilai status sebagai string
        $statusVal = $booking->status instanceof ServiceBookingStatus
            ? $booking->status->value
            : (string) $booking->getRawOriginal('status');

        // Tolak hanya jika booking sudah completed atau cancelled
        if (in_array($statusVal, ['completed', 'cancelled'], true)) {
            throw new \InvalidArgumentException(
                "Booking dengan status {$statusVal} tidak bisa dikonversi ke work order."
            );
        }

        return DB::transaction(function () use ($booking, $mechanicId) {
            // Tentukan mekanik: jika tidak ditentukan dan user saat ini adalah mechanic, assign ke user ini
            $assignedMechanicId = $mechanicId;
            if (!$assignedMechanicId && auth()->check() && auth()->user()->role === 'mechanic') {
                $assignedMechanicId = auth()->id();
            }

            // Buat work order
            $workOrder = WorkOrder::create([
                'code'                => $this->generateWorkOrderCode(),
                'service_booking_id'  => $booking->id,
                'customer_vehicle_id' => $booking->customer_vehicle_id,
                'status'              => WorkOrderStatus::InProgress,
                'assigned_mechanic_id'=> $assignedMechanicId,
                'notes'               => $booking->notes,
            ]);

            // Update booking status menjadi in_progress
            $booking->update(['status' => ServiceBookingStatus::InProgress]);

            return $workOrder;
        });
    }

    public function complete(ServiceBooking $booking): ServiceBooking
    {
        $statusVal = $booking->status instanceof ServiceBookingStatus ? $booking->status->value : (string) $booking->status;
        if ($statusVal !== 'in_progress') {
            throw new \InvalidArgumentException('Hanya booking in_progress yang bisa diselesaikan.');
        }

        $booking->update(['status' => ServiceBookingStatus::Completed]);
        return $booking->fresh();
    }


    protected function calculateEstimatedCost(string $serviceType): int
    {
        $costs = [
            'regular_service' => 500000,
            'heavy_repair' => 2000000,
            'body_repair' => 3000000,
            'ac_service' => 800000,
            'inspection' => 300000,
        ];


        return $costs[$serviceType] ?? 500000;
    }


    protected function generateWorkOrderCode(): string
    {
        do {
            $code = 'WO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (WorkOrder::where('code', $code)->exists());


        return $code;
    }
}



