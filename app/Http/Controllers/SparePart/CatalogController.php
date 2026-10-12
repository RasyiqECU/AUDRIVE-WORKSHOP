<?php


declare(strict_types=1);


namespace App\Http\Controllers\SparePart;


use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Services\SparePartService;
use Illuminate\Http\Request;
use Illuminate\View\View;


class CatalogController extends Controller
{
    public function __construct(
        protected SparePartService $sparePartService
    ) {}


    public function index(Request $request): View
    {
        $filters = [
            'category_id' => $request->input('category_id'),
            'vehicle_brand' => $request->input('vehicle_brand'),
            'vehicle_model' => $request->input('vehicle_model'),
            'vehicle_year' => $request->input('vehicle_year'),
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'name_asc'),
        ];


        $parts = $this->sparePartService->getPublicCatalog($filters);
        $categories = SparePartCategory::orderBy('name')->get();


        return view('spare-parts.index', compact('parts', 'categories', 'filters'));
    }


    public function show(SparePart $sparePart): View
    {
        if ($sparePart->status->value !== 'active') {
            abort(404);
        }


        $sparePart->load(['category', 'compatibilities']);


        // Get related parts
        $relatedParts = SparePart::where('category_id', $sparePart->category_id)
            ->where('id', '!=', $sparePart->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();


        return view('spare-parts.show', compact('sparePart', 'relatedParts'));
    }


    public function byCategory(SparePartCategory $category): View
    {
        $parts = SparePart::where('category_id', $category->id)
            ->where('status', 'active')
            ->with('category')
            ->paginate(12);


        return view('spare-parts.category', compact('category', 'parts'));
    }


    public function compatibility(Request $request): View
    {
        $parts = SparePart::where('status', 'active')
            ->whereHas('compatibilities', function ($query) use ($request) {
                $query->where('vehicle_brand', $request->vehicle_brand);


                if ($request->vehicle_model) {
                    $query->where('vehicle_model', $request->vehicle_model);
                }


                if ($request->vehicle_year) {
                    $query->where('year_from', '<=', $request->vehicle_year)
                          ->where('year_to', '>=', $request->vehicle_year);
                }
            })
            ->with('category')
            ->paginate(12);


        return view('spare-parts.compatibility', compact('parts'));
    }
}



