<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\WorkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartRequestController extends Controller
{
    public function __construct(protected WorkOrderService $workOrderService) {}

    public function index(): View
    {
        $workOrders = WorkOrder::whereHas('items', fn ($q) => $q->where('approval_status', 'pending'))
            ->with(['vehicle.customer.user', 'mechanic', 'items' => fn ($q) => $q->where('approval_status', 'pending')->with('sparePart')])
            ->latest()
            ->get();

        return view('admin.part-requests.index', compact('workOrders'));
    }

    public function check(Request $request, WorkOrderItem $item): RedirectResponse
    {
        $data = $request->validate(['install_fee_idr' => 'nullable|integer|min:0']);

        try {
            $this->workOrderService->markPartChecked(
                $item,
                isset($data['install_fee_idr']) ? (int) $data['install_fee_idr'] : null
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Stok dan harga sudah dicek. Sales dapat menghubungi customer.');
    }

    public function decide(Request $request, WorkOrderItem $item): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:buy,bring_own,skip',
            'install_fee_idr' => 'required_if:decision,bring_own|nullable|integer|min:0',
            'note' => 'required_if:decision,skip|nullable|string|max:255',
        ], [
            'install_fee_idr.required_if' => 'Isi biaya jasa pasang untuk part bawaan customer.',
            'note.required_if' => 'Isi alasan customer tidak mengganti part.',
        ]);

        try {
            $this->workOrderService->decidePart(
                $item,
                $data['decision'],
                (int) ($data['install_fee_idr'] ?? 0),
                $data['note'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Keputusan customer dicatat.');
    }
}
