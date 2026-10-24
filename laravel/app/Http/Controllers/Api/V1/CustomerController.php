<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\Api\V1\StoreVehicleRequest;
use App\Http\Resources\CustomerVehicleResource;
use App\Http\Resources\LoyaltyTransactionResource;
use App\Models\CustomerVehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CustomerController extends ApiController
{
    /**
     * List customer vehicles
     */
    public function vehicles(Request $request): JsonResponse
    {
        $customer = $request->user()->customer;


        $vehicles = CustomerVehicle::where('customer_id', $customer->id)
            ->latest()
            ->paginate($request->input('per_page', 10));


        return $this->paginatedResponse(
            $vehicles,
            CustomerVehicleResource::collection($vehicles),
            'Vehicles retrieved successfully'
        );
    }


    /**
     * Create vehicle
     */
    public function storeVehicle(StoreVehicleRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;


        $vehicle = CustomerVehicle::create([
            'customer_id' => $customer->id,
            'plate_number' => $request->plate_number,
            'vin' => $request->vin,
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'color' => $request->color,
            'notes' => $request->notes,
        ]);


        return $this->successResponse(
            new CustomerVehicleResource($vehicle),
            'Vehicle created successfully',
            201
        );
    }


    /**
     * Get loyalty info
     */
    public function loyalty(Request $request): JsonResponse
    {
        $customer = $request->user()->customer;


        $transactions = $customer->loyaltyTransactions()
            ->latest()
            ->limit(20)
            ->get();


        return $this->successResponse([
            'total_points' => $customer->loyalty_points,
            'transactions' => LoyaltyTransactionResource::collection($transactions),
        ], 'Loyalty info retrieved successfully');
    }
}



