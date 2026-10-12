<?php


declare(strict_types=1);


namespace App\Http\Controllers\Customer;


use App\Http\Controllers\Controller;
use App\Jobs\ProcessAiScanJob;
use App\Models\AiScan;
use App\Models\CustomerVehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;


class AiScanController extends Controller
{
    /**
     * Display upload form
     */
    public function create(Request $request): View
    {
        $vehicles = CustomerVehicle::where('customer_id', $request->user()->customer->id)->get();


        return view('customer.ai-scan.create', compact('vehicles'));
    }


    /**
     * Store new scan
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,m4a|max:20480', // 20MB
            'customer_vehicle_id' => 'nullable|exists:customer_vehicles,id',
        ]);


        $customer = $request->user()->customer;


        // Validate vehicle ownership if provided
        if ($request->customer_vehicle_id) {
            $vehicle = CustomerVehicle::where('id', $request->customer_vehicle_id)
                ->where('customer_id', $customer->id)
                ->first();


            if (!$vehicle) {
                return back()->with('error', 'Kendaraan tidak valid.');
            }
        }


        // Store audio file
        $audioPath = $request->file('audio')->store('ai-scans/audio', 'public');


        // Create scan record
        $scan = AiScan::create([
            'customer_id' => $customer->id,
            'customer_vehicle_id' => $request->customer_vehicle_id,
            'uploaded_by' => $request->user()->id,
            'audio_path' => $audioPath,
            'status' => 'pending',
            'predicted_class' => null,
            'confidence' => null,
            'recommendations' => null,
            'spectrogram_path' => null,
            'technician_review' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);


        // Dispatch job to process scan
        ProcessAiScanJob::dispatch($scan);


        return redirect()
            ->route('customer.ai-scan.show', $scan)
            ->with('success', 'Audio berhasil diupload. Scan sedang diproses.');
    }


    /**
     * Display scan result
     */
    public function show(Request $request, AiScan $aiScan): View
    {
        // Verify ownership
        if ($aiScan->customer_id !== $request->user()->customer->id) {
            abort(403, 'Unauthorized');
        }


        $aiScan->load('vehicle');


        return view('customer.ai-scan.show', compact('aiScan'));
    }


    /**
     * Display scan history
     */
    public function index(Request $request): View
    {
        $scans = AiScan::where('customer_id', $request->user()->customer->id)
            ->with('vehicle')
            ->latest()
            ->paginate(10);


        return view('customer.ai-scan.index', compact('scans'));
    }
}



