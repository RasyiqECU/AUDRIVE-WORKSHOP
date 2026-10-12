<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Resources\SparePartResource;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SparePartController extends ApiController
{
    /**
     * List spare parts
     */
    public function index(Request $request): JsonResponse
    {
        $parts = SparePart::query()
            ->where('status', 'active')
            ->when($request->category_id, fn($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->sort, function ($q, $sort) {
                return match($sort) {
                    'price_asc' => $q->orderBy('price_idr', 'asc'),
                    'price_desc' => $q->orderBy('price_idr', 'desc'),
                    'name_asc' => $q->orderBy('name', 'asc'),
                    'name_desc' => $q->orderBy('name', 'desc'),
                    default => $q->orderBy('name', 'asc'),
                };
            })
            ->with('category')
            ->paginate($request->input('per_page', 12));


        return $this->paginatedResponse(
            $parts,
            SparePartResource::collection($parts),
            'Spare parts retrieved successfully'
        );
    }


    /**
     * Get spare part detail
     */
    public function show(int $id): JsonResponse
    {
        $part = SparePart::with(['category', 'compatibilities'])
            ->where('status', 'active')
            ->findOrFail($id);


        return $this->successResponse(
            new SparePartResource($part),
            'Spare part detail retrieved successfully'
        );
    }


    /**
     * List categories
     */
    public function categories(): JsonResponse
    {
        $categories = SparePartCategory::orderBy('name')->get();


        return $this->successResponse(
            $categories,
            'Categories retrieved successfully'
        );
    }
}



