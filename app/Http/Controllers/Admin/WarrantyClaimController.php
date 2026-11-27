<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\WarrantyClaim;
use App\Services\WarrantyClaimService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyClaimController extends Controller
{
    public function __construct(protected WarrantyClaimService $claims) {}

    public function index(): View
    {
        return view('admin.warranty-claims.index', [
            'claims' => WarrantyClaim::with(['customer.user', 'vehicle', 'repairWorkOrder'])->latest()->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        $number = (string) $request->query('number', '');
        $transaction = $number !== '' ? $this->claims->findTransaction($number) : null;

        return view('admin.warranty-claims.create', [
            'number' => $number,
            'transaction' => $transaction,
            'startsAt' => $transaction ? $this->claims->referenceDate($transaction) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'number' => 'required|string|max:50',
            'work_order_item_id' => 'nullable|integer',
            'reference_km' => 'nullable|integer|min:0',
            'current_km' => 'required|integer|min:0',
            'complaint' => 'required|string|max:2000',
        ]);

        $transaction = $this->claims->findTransaction($data['number']);
        if (! $transaction) {
            return back()->withInput()->with('error', 'Nomor nota tidak ditemukan.');
        }

        try {
            $claim = $this->claims->create($transaction, $data, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.warranty-claims.show', $claim)->with('success', 'Klaim garansi dibuat.');
    }

    public function show(WarrantyClaim $warrantyClaim): View
    {
        $warrantyClaim->load(['customer.user', 'vehicle', 'sourceItem.workOrder', 'sourceOrder', 'diagnoser', 'repairWorkOrder.items']);

        return view('admin.warranty-claims.show', [
            'claim' => $warrantyClaim,
            'spareParts' => SparePart::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function diagnose(Request $request, WarrantyClaim $warrantyClaim): RedirectResponse
    {
        $data = $request->validate(['is_covered' => 'required|boolean', 'diagnosis' => 'required|string|max:2000']);

        return $this->attempt(
            fn () => $this->claims->diagnose($warrantyClaim, $request->user(), (bool) $data['is_covered'], $data['diagnosis']),
            'Diagnosa disimpan.'
        );
    }

    public function decide(Request $request, WarrantyClaim $warrantyClaim): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:decision,reject|nullable|string|max:1000',
        ], ['rejection_reason.required_if' => 'Alasan penolakan wajib diisi.']);

        return $this->attempt(
            fn () => $data['decision'] === 'approve'
                ? $this->claims->approve($warrantyClaim, $request->user())
                : $this->claims->reject($warrantyClaim, $request->user(), $data['rejection_reason']),
            'Keputusan klaim disimpan.'
        );
    }

    public function issuePart(Request $request, WarrantyClaim $warrantyClaim): RedirectResponse
    {
        $data = $request->validate(['spare_part_id' => 'required|exists:spare_parts,id', 'qty' => 'required|integer|min:1']);

        return $this->attempt(
            fn () => $this->claims->issuePart($warrantyClaim, SparePart::findOrFail($data['spare_part_id']), (int) $data['qty']),
            'Suku cadang garansi dikeluarkan.'
        );
    }

    private function attempt(Closure $action, string $success): RedirectResponse
    {
        try {
            $action();
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $success);
    }
}
