<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\Api\V1\StoreOfferRequest;
use App\Http\Requests\Api\V1\StoreTestDriveRequest;
use App\Http\Resources\CarUnitResource;
use App\Http\Resources\OfferResource;
use App\Models\CarUnit;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class MarketplaceController extends ApiController
{
    protected OfferService $offerService;


    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }


    /**
     * List cars
     */
    public function cars(Request $request): JsonResponse
    {
        $cars = CarUnit::query()
            ->when($request->brand, fn($q, $brand) => $q->where('brand', $brand))
            ->when($request->year, fn($q, $year) => $q->where('year', $year))
            ->when($request->transmission, fn($q, $trans) => $q->where('transmission', $trans))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->min_price, fn($q, $min) => $q->where('price_sell_idr', '>=', $min))
            ->when($request->max_price, fn($q, $max) => $q->where('price_sell_idr', '<=', $max))
            ->when($request->search, fn($q, $keyword) => $q->search($keyword))
            ->when($request->sort, function ($q, $sort) {
                return match($sort) {
                    'price_asc' => $q->orderBy('price_sell_idr', 'asc'),
                    'price_desc' => $q->orderBy('price_sell_idr', 'desc'),
                    'mileage_asc' => $q->orderBy('mileage_km', 'asc'),
                    'year_desc' => $q->orderBy('year', 'desc'),
                    default => $q->latest(),
                };
            }, fn($q) => $q->latest())
            ->with('primaryPhoto')
            ->paginate($request->input('per_page', 12));


        return $this->paginatedResponse(
            $cars,
            CarUnitResource::collection($cars),
            'Cars retrieved successfully'
        );
    }


    /**
     * Get car detail
     */
    public function carDetail(int $id): JsonResponse
    {
        $car = CarUnit::with(['photos', 'latestInspection.inspector'])->findOrFail($id);


        return $this->successResponse(
            new CarUnitResource($car),
            'Car detail retrieved successfully'
        );
    }


    /**
     * Create test drive booking
     */
    public function storeTestDrive(StoreTestDriveRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;
        $car = CarUnit::findOrFail($request->car_unit_id);


        try {
            $appointment = \App\Models\Appointment::create([
                'type' => 'test_drive',
                'car_unit_id' => $car->id,
                'customer_id' => $customer->id,
                'scheduled_at' => $request->scheduled_at,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);


            return $this->successResponse($appointment, 'Test drive booked successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * Create offer
     */
    public function storeOffer(StoreOfferRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;
        $car = CarUnit::findOrFail($request->car_unit_id);


        try {
            $offer = $this->offerService->create(
                $customer,
                $car,
                $request->amount_idr,
                $request->note
            );


            return $this->successResponse(
                new OfferResource($offer),
                'Offer created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * List customer offers
     */
    public function offers(Request $request): JsonResponse
    {
        $customer = $request->user()->customer;


        $offers = Offer::where('customer_id', $customer->id)
            ->with('carUnit')
            ->latest()
            ->paginate($request->input('per_page', 10));


        return $this->paginatedResponse(
            $offers,
            OfferResource::collection($offers),
            'Offers retrieved successfully'
        );
    }
}



