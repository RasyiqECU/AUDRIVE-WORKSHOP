<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workshop;

use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\WorkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkOrderTrackingController extends Controller
{
    /**
     * Tampilkan status dan detail pengerjaan work order.
     */
    public function show(Request $request, WorkOrder $workOrder): View
    {
        $user = $request->user();

        // Pastikan work order terkait dengan kendaraan milik customer jika diakses oleh role customer
        if ($user->role === 'customer') {
            $customer = $user->customer;
            $vehicleCustomerId = $workOrder->vehicle?->customer_id;
            $bookingCustomerId = $workOrder->serviceBooking?->customer_id;

            if (! $customer || ($vehicleCustomerId !== $customer->id && $bookingCustomerId !== $customer->id)) {
                abort(403, 'Akses tidak diizinkan.');
            }
        }

        $workOrder->load(['vehicle.customer', 'serviceBooking', 'items', 'mechanic']);

        // Riwayat waktu transisi status dari AuditLog
        $statusLogs = AuditLog::where('model_id', $workOrder->id)
            ->whereIn('action', ['work_order.status_updated', 'status_changed'])
            ->orderBy('created_at', 'asc')
            ->get();

        $statusTimestamps = [];
        foreach ($statusLogs as $log) {
            if (preg_match('/to ([a-z_]+)$/i', trim($log->description ?? ''), $matches)) {
                $statusTimestamps[strtolower($matches[1])] = $log->created_at;
            }
        }

        return view('workshop.tracking', compact('workOrder', 'statusTimestamps'));
    }

    /** Customer sendiri memilih: beli di bengkel, bawa sendiri, atau tidak diganti. */
    public function decidePart(Request $request, WorkOrderItem $item, WorkOrderService $workOrders): RedirectResponse
    {
        $this->authorizeOwner($request, $item->workOrder);

        $data = $request->validate(['decision' => 'required|in:buy,bring_own,skip', 'note' => 'nullable|string|max:255']);

        // Biaya jasa pasang ditentukan bengkel, bukan oleh customer
        if ($data['decision'] === 'bring_own' && $item->install_fee_idr === null) {
            return back()->with('error', 'Biaya jasa pasang belum ditentukan. Hubungi Sales kami.');
        }

        try {
            $workOrders->decidePart(
                $item,
                $data['decision'],
                (int) $item->install_fee_idr,
                $data['note'] ?? ($data['decision'] === 'skip' ? 'Ditolak customer lewat portal' : null)
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pilihan Anda sudah dicatat.');
    }

    /** Pilih bayar tagihan servis lewat transfer. */
    public function payOnline(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorizeOwner($request, $workOrder);

        $order = $workOrder->salesOrder;
        if (! $order || $order->status !== SalesOrderStatus::Pending || $order->total_idr <= 0) {
            return back()->with('error', 'Tidak ada tagihan yang bisa dibayar online.');
        }

        if (! $order->isOnlineTransfer()) {
            $order->update(['payment_channel' => 'online_transfer', 'payment_status' => PaymentStatus::WaitingUpload->value]);
        }

        return redirect()->route('parts.checkout.payment', $order);
    }

    private function authorizeOwner(Request $request, WorkOrder $workOrder): void
    {
        $customer = $request->user()->customer;
        $ownerId = $workOrder->vehicle?->customer_id ?? $workOrder->serviceBooking?->customer_id;

        abort_unless($customer && $ownerId === $customer->id, 403);
    }

    /**
     * Tampilkan timeline progress servis work order.
     */
    public function timeline(Request $request, WorkOrder $workOrder): View
    {
        return $this->show($request, $workOrder);
    }
}
