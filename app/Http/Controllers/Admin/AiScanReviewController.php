<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AiScanStatus;
use App\Http\Controllers\Controller;
use App\Models\AiScan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AiScanReviewController extends Controller
{
    /**
     * Daftar semua scan — prioritaskan yang perlu review.
     */
    public function index(Request $request): View
    {
        $query = AiScan::with(['customer.user', 'vehicle']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency_level', $request->urgency);
        }

        if ($request->filled('predicted_class')) {
            $query->where('predicted_class', $request->predicted_class);
        }

        $scans = $query->latest()->paginate(15);

        return view('admin.ai-scans.index', compact('scans'));
    }


    /**
     * Detail scan — tampilkan dual diagnosis (AI + Manual).
     * Langkah 4: mekanik/admin bisa lihat hasil AI dan audio, lalu beri keputusan final.
     */
    public function show(AiScan $aiScan): View
    {
        $aiScan->load(['customer.user', 'vehicle', 'uploader', 'reviewer']);

        return view('admin.ai-scans.show', compact('aiScan'));
    }


    /**
     * Simpan review manual (Langkah 4 — Manual Review oleh Mekanik/Super Admin).
     * Mendukung: approve AI result, atau override dengan keputusan manual.
     */
    public function review(Request $request, AiScan $aiScan): RedirectResponse
    {
        $request->validate([
            'technician_review'  => 'required|string|max:2000',
            'mechanic_decision'  => 'nullable|string|max:100',
            'mechanic_final_notes' => 'nullable|string|max:2000',
            'final_urgency'      => 'nullable|in:safe,monitor,urgent,emergency',
            'action'             => 'required|in:approve,override',
        ]);

        $isOverride = $request->action === 'override';

        // Tentukan final diagnosis
        $finalDiagnosis = $isOverride
            ? ($request->mechanic_decision ?? $aiScan->predicted_class)
            : $aiScan->predicted_class;

        $finalUrgency = $isOverride
            ? ($request->final_urgency ?? $aiScan->urgency_level)
            : $aiScan->urgency_level;

        $aiScan->update([
            'technician_review'  => $request->technician_review,
            'mechanic_decision'  => $request->mechanic_decision,
            'mechanic_final_notes' => $request->mechanic_final_notes,
            'final_diagnosis'    => $finalDiagnosis,
            'final_urgency'      => $finalUrgency,
            'is_confirmed'       => true,
            'reviewed_by'        => $request->user()->id,
            'reviewed_at'        => now(),
            'status'             => AiScanStatus::Reviewed,
        ]);

        $message = $isOverride
            ? 'Review disimpan. Hasil AI telah dikoreksi oleh mekanik.'
            : 'Review disimpan. Hasil AI dikonfirmasi dan disetujui.';

        return redirect()
            ->route('admin.ai-scans.show', $aiScan)
            ->with('success', $message);
    }


    /**
     * History scan untuk kendaraan tertentu.
     */
    public function vehicleHistory(Request $request, int $vehicleId): View
    {
        $scans = AiScan::where('customer_vehicle_id', $vehicleId)
            ->with('customer.user')
            ->latest()
            ->paginate(10);

        return view('admin.ai-scans.vehicle-history', compact('scans', 'vehicleId'));
    }
}
