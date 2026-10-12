<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\Customer;
use Carbon\Carbon;


class AppointmentService
{
    public function createTestDrive(
        Customer $customer,
        CarUnit $carUnit,
        Carbon $scheduledAt,
        ?string $notes = null
    ): Appointment {
        // Validasi: unit harus available
        if ($carUnit->status !== CarUnitStatus::Available) {
            throw new \InvalidArgumentException('Unit tidak tersedia untuk test drive.');
        }


        // Validasi: jadwal tidak boleh di masa lalu
        if ($scheduledAt->isPast()) {
            throw new \InvalidArgumentException('Jadwal test drive tidak boleh di masa lalu.');
        }


        return Appointment::create([
            'type' => AppointmentType::TestDrive,
            'car_unit_id' => $carUnit->id,
            'customer_id' => $customer->id,
            'scheduled_at' => $scheduledAt,
            'status' => AppointmentStatus::Pending,
            'notes' => $notes,
        ]);
    }


    public function createInspection(
        Customer $customer,
        CarUnit $carUnit,
        Carbon $scheduledAt,
        ?string $notes = null
    ): Appointment {
        // Validasi: jadwal tidak boleh di masa lalu
        if ($scheduledAt->isPast()) {
            throw new \InvalidArgumentException('Jadwal inspeksi tidak boleh di masa lalu.');
        }


        return Appointment::create([
            'type' => AppointmentType::Inspection,
            'car_unit_id' => $carUnit->id,
            'customer_id' => $customer->id,
            'scheduled_at' => $scheduledAt,
            'status' => AppointmentStatus::Pending,
            'notes' => $notes,
        ]);
    }


    public function confirm(Appointment $appointment): Appointment
    {
        if ($appointment->status !== AppointmentStatus::Pending) {
            throw new \InvalidArgumentException('Hanya appointment pending yang bisa dikonfirmasi.');
        }


        return $appointment->update(['status' => AppointmentStatus::Confirmed]);
    }


    public function complete(Appointment $appointment): Appointment
    {
        if ($appointment->status !== AppointmentStatus::Confirmed) {
            throw new \InvalidArgumentException('Hanya appointment confirmed yang bisa diselesaikan.');
        }


        return $appointment->update(['status' => AppointmentStatus::Completed]);
    }


    public function cancel(Appointment $appointment): Appointment
    {
        if (in_array($appointment->status, [AppointmentStatus::Completed, AppointmentStatus::Cancelled])) {
            throw new \InvalidArgumentException('Appointment tidak bisa dibatalkan.');
        }


        return $appointment->update(['status' => AppointmentStatus::Cancelled]);
    }


    public function getAvailableSlots(Carbon $date, int $durationMinutes = 60): array
    {
        $startTime = $date->copy()->setTime(9, 0); // Buka jam 9
        $endTime = $date->copy()->setTime(17, 0); // Tutup jam 5
        $slots = [];


        while ($startTime < $endTime) {
            $slotEnd = $startTime->copy()->addMinutes($durationMinutes);


            // Cek apakah slot sudah ada appointment
            $hasAppointment = Appointment::where('scheduled_at', '>=', $startTime)
                ->where('scheduled_at', '<', $slotEnd)
                ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])
                ->exists();


            if (!$hasAppointment) {
                $slots[] = [
                    'start' => $startTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'available' => true,
                ];
            }


            $startTime->addMinutes($durationMinutes);
        }


        return $slots;
    }
}



