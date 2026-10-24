<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\Api\V1\StoreAiScanRequest;
use App\Http\Resources\AiScanResource;
use App\Jobs\ProcessAiScanJob;
use App\Models\AiScan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AiScanController extends ApiController
{
    /**
     * List AI scans
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user()->customer;


        $scans = AiScan::where('customer_id', $customer->id)
            ->with('vehicle')
            ->latest()
            ->paginate($request->input('per_page', 10));


        return $this->paginatedResponse(
            $scans,
            AiScanResource::collection($scans),
            'AI scans retrieved successfully'
        );
    }


    /**
     * Create AI scan
     */
    public function store(StoreAiScanRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;


        // Store audio file
        $audioPath = $request->file('audio')->store('ai-scans/audio', 'public');


        // Create scan record
        $scan = AiScan::create([
            'customer_id' => $customer->id,
            'customer_vehicle_id' => $request->customer_vehicle_id,
            'uploaded_by' => $request->user()->id,
            'audio_path' => $audioPath,
            'status' => 'pending',
        ]);


        // Dispatch job to process scan
        ProcessAiScanJob::dispatch($scan);


        return $this->successResponse(
            new AiScanResource($scan),
            'AI scan created successfully. Processing in background.',
            201
        );
    }


    /**
     * Get AI scan detail
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $customer = $request->user()->customer;


        $scan = AiScan::where('customer_id', $customer->id)
            ->with('vehicle')
            ->findOrFail($id);


        return $this->successResponse(
            new AiScanResource($scan),
            'AI scan detail retrieved successfully'
        );
    }
}



