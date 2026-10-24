<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
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
     * Approve request
     */
    public function approve(Request $request, ApprovalRequest $approval): RedirectResponse
    {
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
                ->with('error', 'Gagal memproses approval: ' . $e->getMessage());
        }
    }


    /**
     * Reject request
     */
    public function reject(Request $request, ApprovalRequest $approval): RedirectResponse
    {
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
                ->with('error', 'Gagal memproses approval: ' . $e->getMessage());
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
}



