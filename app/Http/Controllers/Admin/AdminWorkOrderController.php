<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\ServiceBookingService;
use App\Services\WorkOrderService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminWorkOrderController extends Controller
{
    public function __construct(
        protected WorkOrderService $workOrderService,
        protected ServiceBookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $workOrders = WorkOrder::query()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($user->role === 'mechanic', fn ($q) => $q->where(fn ($own) => $own
                ->where('assigned_mechanic_id', $user->id)
                ->orWhereNull('assigned_mechanic_id')))
            ->with(['vehicle.customer.user', 'mechanic'])
            ->latest()
            ->paginate(15);

        return view('admin.work-orders.index', compact('workOrders'));
    }

    public function show(Request $request, WorkOrder $workOrder): View
    {
        $user = $request->user();
        if ($user->role === 'mechanic' && ! in_array($workOrder->assigned_mechanic_id, [null, $user->id], true)) {
            abort(403, 'Akses ditolak: Kepala Mekanik hanya dapat melihat Work Order yang ditugaskan kepada dirinya.');
        }

        $workOrder->load([
            'vehicle.customer.user',
            'mechanic',
            'items.sparePart',
            'serviceBooking',
        ]);

        $spareParts = SparePart::where('status', 'active')->orderBy('name')->get();
        $mechanics = User::where('role', 'mechanic')->orderBy('name')->get();

        return view('admin.work-orders.show', compact('workOrder', 'spareParts', 'mechanics'));
    }

    public function convertBooking(Request $request, ServiceBooking $serviceBooking): RedirectResponse
    {
        $this->authorize('create', WorkOrder::class);

        $data = $request->validate([
            'mechanic_id' => ['required', Rule::exists('users', 'id')->where('role', 'mechanic')],
            'estimated_completion_at' => ['required', 'date', 'after:now'],
        ]);

        try {
            $workOrder = $this->bookingService->convertToWorkOrder(
                $serviceBooking,
                (int) $data['mechanic_id'],
                Carbon::parse($data['estimated_completion_at'])
            );

            // Work order milik kepala mekanik lain tidak bisa dibuka pembuatnya
            if ($request->user()->cannot('view', $workOrder)) {
                return back()->with('success', "Work order {$workOrder->code} dibuat dan ditugaskan.");
            }

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Booking berhasil dikonversi menjadi work order.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('updateStatus', $workOrder);

        $request->validate([
            'status' => 'required|in:'.implode(',', array_column(WorkOrderStatus::cases(), 'value')),
        ]);

        $target = WorkOrderStatus::from($request->status);
        $allowed = match ($request->user()->role) {
            'mechanic' => [WorkOrderStatus::InProgress, WorkOrderStatus::QualityControl],
            default => WorkOrderStatus::cases(),
        };

        if (! in_array($target, $allowed, true)) {
            return back()->with('error', 'Anda tidak berwenang mengubah status ke '.$target->label().'. Selesai ditetapkan lewat tombol Lolos QC, serah terima setelah lunas.');
        }

        try {
            // Selesai & diserahkan punya syarat sendiri (QC / lunas)
            match ($target) {
                WorkOrderStatus::Completed => $this->workOrderService->complete($workOrder),
                WorkOrderStatus::Delivered => $this->workOrderService->deliver($workOrder),
                default => $this->workOrderService->updateStatus($workOrder, $target),
            };

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Status work order berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addServiceItem(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'unit_price_idr' => 'required|integer|min:0',
        ]);

        try {
            $this->workOrderService->addServiceItem(
                $workOrder,
                (string) $validated['description'],
                (int) $validated['qty'],
                (int) $validated['unit_price_idr']
            );

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Item jasa berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addPartItem(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);

        $validated = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'qty' => 'required|integer|min:1',
        ]);

        $sparePart = SparePart::findOrFail($validated['spare_part_id']);

        try {
            $this->workOrderService->addPartItem(
                $workOrder,
                $sparePart,
                (int) $validated['qty']
            );

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Usulan spare part dikirim. Menunggu cek gudang dan konfirmasi customer.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeItem(Request $request, WorkOrderItem $item): RedirectResponse
    {
        $this->authorize('update', $item->workOrder);

        // Part yang sudah disetujui customer hanya bisa dihapus Super Admin
        if ($item->part_source && $request->user()->role !== 'super-admin') {
            return back()->with('error', 'Item yang sudah diputuskan customer tidak bisa dihapus.');
        }

        try {
            $this->workOrderService->removeItem($item);

            return redirect()
                ->route('admin.work-orders.show', $item->workOrder)
                ->with('success', 'Item berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('qualityControl', $workOrder);

        try {
            $this->workOrderService->complete($workOrder);

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Work order berhasil diselesaikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deliver(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('deliver', $workOrder);

        try {
            $this->workOrderService->deliver($workOrder);

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Work order telah diserahkan ke pelanggan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create(): RedirectResponse
    {
        $this->authorize('create', WorkOrder::class);

        return redirect()->route('admin.service-bookings.index')->with('info', 'Silakan pilih booking untuk dikonversi menjadi Work Order.');
    }

    public function assignMechanic(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('assignMechanic', $workOrder);

        $validated = $request->validate([
            'mechanic_id' => ['required', Rule::exists('users', 'id')->where('role', 'mechanic')],
        ]);

        $workOrder->update([
            'assigned_mechanic_id' => $validated['mechanic_id'],
        ]);

        $redirect = $request->user()->can('view', $workOrder->refresh())
            ? back()
            : redirect()->route('admin.work-orders.index');

        return $redirect->with('success', 'Kepala Mekanik berhasil ditugaskan.');
    }

    public function destroy(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('delete', $workOrder);

        $workOrder->delete();

        return redirect()->route('admin.work-orders.index')->with('success', 'Work Order berhasil dihapus.');
    }
}
