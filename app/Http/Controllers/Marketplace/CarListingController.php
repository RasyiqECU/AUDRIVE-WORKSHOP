<?php


declare(strict_types=1);


namespace App\Http\Controllers\Marketplace;


use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;


class CarListingController extends Controller
{
    public function index(Request $request): View
    {
        $cars = CarUnit::query()
            ->when($request->brand, fn($q, $brand) => $q->byBrand($brand))
            ->when($request->year, fn($q, $year) => $q->byYear($year))
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
            ->paginate(12)
            ->withQueryString();


        $brands = CarUnit::query()
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');


        return view('marketplace.index', compact('cars', 'brands'));
    }


    public function show(CarUnit $car): View
    {
        $car->load(['photos', 'latestInspection.inspector', 'offers']);


        $relatedCars = CarUnit::query()
            ->where('brand', $car->brand)
            ->where('id', '!=', $car->id)
            ->available()
            ->with('primaryPhoto')
            ->limit(4)
            ->get();

        return view('marketplace.show', compact('car', 'relatedCars'));
    }

    public function search(Request $request): View
    {
        return $this->index($request);
    }

    public function sellCreate(): View
    {
        return view('marketplace.sell');
    }

    public function sellStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year'  => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'expected_price' => 'required|numeric',
        ]);

        return redirect()->route('marketplace.index')->with('success', 'Pengajuan penjualan mobil Anda berhasil dikirim. Tim kami akan menghubungi Anda.');
    }
}



