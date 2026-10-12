<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\ServiceBookingService;
use App\Services\WorkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AdminWorkOrderController extends Controller
{
    public function __construct(
        protected WorkOrderService $workOrderService,
        protected ServiceBookingService $bookingService
    ) {}


    public function index(Request $request): View
    {
        $workOrders = WorkOrder::query()
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->with(['vehicle.customer.user', 'mechanic'])
            ->latest()
            ->paginate(15);


        return view('admin.work-orders.index', compact('workOrders'));
    }


    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load([
            'vehicle.customer.user',
            'mechanic',
            'items.sparePart',
            'serviceBooking',
        ]);


        return view('admin.work-orders.show', compact('workOrder'));
    }


    public function convertBooking(Request $request, ServiceBooking $serviceBooking): RedirectResponse
    {
        $this->authorize('manage', WorkOrder::class);

        try {
            $workOrder = $this->bookingService->convertToWorkOrder(
                $serviceBooking,
                $request->filled('mechanic_id') ? (int) $request->mechanic_id : null
            );

            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Booking berhasil dikonversi menjadi work order.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function updateStatus(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);


        $request->validate([
            'status' => 'required|in:' . implode(',', array_column(WorkOrderStatus::cases(), 'value')),
        ]);


        try {
            $this->workOrderService->updateStatus(
                $workOrder,
                WorkOrderStatus::from($request->status)
            );


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
                ->with('success', 'Spare part berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function removeItem(WorkOrderItem $item): RedirectResponse
    {
        $this->authorize('update', $item->workOrder);


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
        $this->authorize('update', $workOrder);


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
        $this->authorize('update', $workOrder);


        try {
            $this->workOrderService->deliver($workOrder);


            return redirect()
                ->route('admin.work-orders.show', $workOrder)
                ->with('success', 'Work order telah diserahkan ke pelanggan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}



