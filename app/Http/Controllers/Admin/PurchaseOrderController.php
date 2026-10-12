<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\SparePart;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $purchaseOrderService
    ) {}


    public function index(Request $request): View
    {
        $orders = PurchaseOrder::with('creator')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);


        return view('admin.purchase-orders.index', compact('orders'));
    }


    public function create(): View
    {
        $parts = SparePart::orderBy('name')->get();
        return view('admin.purchase-orders.create', compact('parts'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'required|exists:spare_parts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.cost_idr' => 'required|integer|min:0',
        ]);


        $po = $this->purchaseOrderService->create($validated, $validated['items']);


        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', 'Purchase order berhasil dibuat.');
    }


    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['items.sparePart', 'creator']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }


    public function send(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrderService->send($purchaseOrder);
            return back()->with('success', 'PO berhasil dikirim ke supplier.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function receive(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.received_qty' => 'required|integer|min:0',
        ]);


        try {
            $receivedItems = [];
            foreach ($request->items as $itemId => $data) {
                $receivedItems[$itemId] = $data['received_qty'];
            }


            $this->purchaseOrderService->receive($purchaseOrder, $receivedItems);
            return back()->with('success', 'Stok berhasil diterima dan ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrderService->cancel($purchaseOrder);
            return back()->with('success', 'PO berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}



