<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\Api\V1\StoreServiceBookingRequest;
use App\Http\Resources\ServiceBookingResource;
use App\Http\Resources\WorkOrderResource;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class WorkshopController extends ApiController
{
    /**
     * List service bookings
     */
    public function serviceBookings(Request $request): JsonResponse
    {
        $customer = $request->user()->customer;


        $bookings = ServiceBooking::where('customer_id', $customer->id)
            ->with('vehicle')
            ->latest()
            ->paginate($request->input('per_page', 10));


        return $this->paginatedResponse(
            $bookings,
            ServiceBookingResource::collection($bookings),
            'Service bookings retrieved successfully'
        );
    }


    /**
     * Create service booking
     */
    public function storeServiceBooking(StoreServiceBookingRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;
        $vehicle = CustomerVehicle::where('id', $request->customer_vehicle_id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();


        try {
            $booking = ServiceBooking::create([
                'customer_id' => $customer->id,
                'customer_vehicle_id' => $vehicle->id,
                'service_type' => $request->service_type,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time,
                'status' => 'pending',
                'estimated_cost_idr' => $this->calculateEstimatedCost($request->service_type),
                'notes' => $request->notes,
            ]);


            return $this->successResponse(
                new ServiceBookingResource($booking),
                'Service booking created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * Get service booking detail
     */
    public function serviceBookingDetail(Request $request, int $id): JsonResponse
    {
        $customer = $request->user()->customer;


        $booking = ServiceBooking::where('customer_id', $customer->id)
            ->with(['vehicle', 'workOrder.items'])
            ->findOrFail($id);


        return $this->successResponse(
            new ServiceBookingResource($booking),
            'Service booking detail retrieved successfully'
        );
    }


    /**
     * Get work order detail
     */
    public function workOrderDetail(Request $request, int $id): JsonResponse
    {
        $customer = $request->user()->customer;


        $workOrder = WorkOrder::whereHas('vehicle', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->with(['vehicle', 'mechanic', 'items.sparePart'])
            ->findOrFail($id);


        return $this->successResponse(
            new WorkOrderResource($workOrder),
            'Work order detail retrieved successfully'
        );
    }


    /**
     * Calculate estimated cost based on service type
     */
    protected function calculateEstimatedCost(string $serviceType): int
    {
        $costs = [
            'regular_service' => 500000,
            'heavy_repair' => 2000000,
            'body_repair' => 3000000,
            'ac_service' => 800000,
            'inspection' => 300000,
        ];


        return $costs[$serviceType] ?? 500000;
    }
}



