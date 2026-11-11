<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Services\ApprovalService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    protected ApprovalService $approvalService;

    protected AuditService $auditService;

    public function __construct(
        ApprovalService $approvalService,
        AuditService $auditService
    ) {
        $this->approvalService = $approvalService;
        $this->auditService = $auditService;
    }

    /**
     * Display list of approval requests
     */
    public function index(Request $request): View
    {
        $query = ApprovalRequest::with(['requester', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $approvals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.approvals.index', compact('approvals'));
    }

    /**
     * Display approval request details
     */
    public function show(ApprovalRequest $approval): View
    {
        $approval->load(['requester', 'approver']);

        return view('admin.approvals.show', compact('approval'));
    }

    /**
     * Form pengajuan approval untuk staf operasional
     * (Sales: diskon, Kasir: refund, Inventory: adjustment harga/stok)
     */
    public function create(Request $request): View
    {
        // Opsi tipe pengajuan dibatasi sesuai role pemohon
        $allowedTypes = match ($request->user()->role) {
            'sales' => ['discount'],
            'cashier' => ['discount', 'refund'],
            'inventory' => ['price_adjustment', 'stock_adjustment'],
            default => ['discount', 'refund', 'price_adjustment', 'stock_adjustment'],
        };

        $salesOrders = SalesOrder::with('customer')
            ->latest()
            ->limit(50)
            ->get();

        $payments = Payment::success()
            ->where('paymentable_type', SalesOrder::class)
            ->with('paymentable')
            ->latest('paid_at')
            ->limit(50)
            ->get();

        $spareParts = SparePart::orderBy('name')->limit(200)->get();

        return view('admin.approvals.create', compact('allowedTypes', 'salesOrders', 'payments', 'spareParts'));
    }

    /**
     * Simpan pengajuan approval baru dari staf operasional.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:discount,refund,price_adjustment,stock_adjustment'],
            'notes' => ['required', 'string', 'max:500'],

            // discount / refund
            'sales_order_id' => ['nullable', 'integer', 'exists:sales_orders,id'],
            'payment_id' => ['nullable', 'integer', 'exists:payments,id'],
            'amount_idr' => ['nullable', 'numeric', 'min:1', 'max:2000000000'],

            // price_adjustment / stock_adjustment
            'spare_part_id' => ['nullable', 'integer', 'exists:spare_parts,id'],
            'new_price_idr' => ['nullable', 'numeric', 'min:0', 'max:2000000000'],
            'new_stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ]);

        $type = $validated['type'];
        $fails = [];

        // Validasi konsistensi field per tipe pengajuan
        switch ($type) {
            case 'discount':
                $this->validateTargetPresence($fails, $validated, ['sales_order_id', 'amount_idr']);
                if (isset($validated['sales_order_id'])) {
                    $order = SalesOrder::find($validated['sales_order_id']);
                    if ($order && (int) $validated['amount_idr'] > $order->total_idr) {
                        $fails[] = 'Nominal diskon tidak boleh melebihi total nilai pesanan.';
                    }
                }
                break;

            case 'refund':
                $this->validateTargetPresence($fails, $validated, ['payment_id', 'amount_idr']);
                if (isset($validated['payment_id'])) {
                    $payment = Payment::find($validated['payment_id']);
                    if ($payment && ! $payment->isSuccessful()) {
                        $fails[] = 'Pembayaran yang dipilih tidak berstatus Berhasil, tidak dapat direfund.';
                    } elseif ($payment && (int) $validated['amount_idr'] > $payment->amount_idr) {
                        $fails[] = 'Nominal refund tidak boleh melebihi jumlah pembayaran.';
                    }
                }
                break;

            case 'price_adjustment':
                $this->validateTargetPresence($fails, $validated, ['spare_part_id', 'new_price_idr']);
                break;

            case 'stock_adjustment':
                $this->validateTargetPresence($fails, $validated, ['spare_part_id', 'new_stock']);
                break;
        }

        if (! empty($fails)) {
            return back()->withInput()->with('error', implode(' ', $fails));
        }

        $payload = match ($type) {
            'discount' => [
                'sales_order_id' => (int) $validated['sales_order_id'],
                'amount_idr' => (int) $validated['amount_idr'],
            ],
            'refund' => [
                'payment_id' => (int) $validated['payment_id'],
                'amount_idr' => (int) $validated['amount_idr'],
            ],
            'price_adjustment' => [
                'spare_part_id' => (int) $validated['spare_part_id'],
                'new_price_idr' => (int) $validated['new_price_idr'],
                'old_price_idr' => (int) SparePart::find($validated['spare_part_id'])->price_idr,
            ],
            default => [
                'spare_part_id' => (int) $validated['spare_part_id'],
                'new_stock' => (int) $validated['new_stock'],
                'old_stock' => (int) SparePart::find($validated['spare_part_id'])->stock,
            ],
        };
        $payload['reason'] = $validated['notes'];

        $this->approvalService->createRequest($type, $payload, $request->user());

        return back()->with('success', 'Pengajuan approval berhasil dikirim ke Manager dan sedang menunggu persetujuan.');
    }

    /**
     * Pastikan field target yang wajib ada untuk tipe pengajuan tertentu sudah diisi.
     *
     * @param  array<int, string>  $fails
     * @param  array<string, mixed>  $validated
     * @param  array<int, string>  $requiredFields
     */
    protected function validateTargetPresence(array &$fails, array $validated, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (! isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                $fails[] = "Field {$field} wajib diisi untuk tipe pengajuan ini.";
            }
        }
    }

    /**
     * Approve request
     */
    public function approve(Request $request, ApprovalRequest $approval): RedirectResponse
    {
        if (($fail = $this->approvalGuard($request, $approval)) !== null) {
            return $fail;
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->approvalService->approve(
                $approval,
                $request->user(),
                $validated['notes'] ?? null
            );

            $this->auditService->logApproval('approved', $approval->id, $request->user());

            return redirect()
                ->route('admin.approvals.index')
                ->with('success', 'Approval berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memproses approval: '.$e->getMessage());
        }
    }

    /**
     * Reject request
     */
    public function reject(Request $request, ApprovalRequest $approval): RedirectResponse
    {
        if (($fail = $this->approvalGuard($request, $approval)) !== null) {
            return $fail;
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $this->approvalService->reject(
                $approval,
                $request->user(),
                $validated['notes']
            );

            $this->auditService->logApproval('rejected', $approval->id, $request->user());

            return redirect()
                ->route('admin.approvals.index')
                ->with('success', 'Approval berhasil ditolak');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memproses approval: '.$e->getMessage());
        }
    }

    /**
     * Get pending approvals count
     */
    public function pendingCount()
    {
        $count = ApprovalRequest::where('status', 'pending')->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Guard penyetuju: tidak boleh menyetujui pengajuannya sendiri.
     * Pemisahan tugas: pembuat pengajuan ≠ penyetuju.
     */
    protected function approvalGuard(Request $request, ApprovalRequest $approval): ?RedirectResponse
    {
        if ($approval->requested_by === $request->user()?->id) {
            return redirect()
                ->route('admin.approvals.index')
                ->with('error', 'Anda tidak dapat menyetujui pengajuan yang Anda buat sendiri.');
        }

        return null;
    }
}
