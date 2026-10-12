<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\AiScan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AiScanReviewController extends Controller
{
    /**
     * Display list of scans for review
     */
    public function index(Request $request): View
    {
        $query = AiScan::with(['customer.user', 'vehicle']);


        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        // Filter by predicted class
        if ($request->filled('predicted_class')) {
            $query->where('predicted_class', $request->predicted_class);
        }


        $scans = $query->latest()->paginate(15);


        return view('admin.ai-scans.index', compact('scans'));
    }


    /**
     * Display scan detail for review
     */
    public function show(AiScan $aiScan): View
    {
        $aiScan->load(['customer.user', 'vehicle', 'uploader']);


        return view('admin.ai-scans.show', compact('aiScan'));
    }


    /**
     * Update scan with technician review
     */
    public function review(Request $request, AiScan $aiScan): RedirectResponse
    {
        $request->validate([
            'technician_review' => 'required|string|max:1000',
            'predicted_class' => 'nullable|string|max:100',
        ]);


        $aiScan->update([
            'technician_review' => $request->technician_review,
            'predicted_class' => $request->predicted_class ?? $aiScan->predicted_class,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'status' => 'reviewed',
        ]);


        return redirect()
            ->route('admin.ai-scans.index')
            ->with('success', 'Review berhasil disimpan.');
    }


    /**
     * Display scan history for a specific vehicle
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



