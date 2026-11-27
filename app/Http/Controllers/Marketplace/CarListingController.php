<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Services\ConsignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarListingController extends Controller
{
    public function index(Request $request): View
    {
        $cars = CarUnit::query()
            ->publiclyListed()
            ->when(in_array($request->condition, ['new', 'used'], true), fn ($q) => $q->where('condition', $request->condition))
            ->when($request->brand, fn ($q, $brand) => $q->byBrand($brand))
            ->when($request->year, fn ($q, $year) => $q->byYear($year))
            ->when($request->transmission, fn ($q, $trans) => $q->where('transmission', $trans))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->min_price, fn ($q, $min) => $q->where('price_sell_idr', '>=', $min))
            ->when($request->max_price, fn ($q, $max) => $q->where('price_sell_idr', '<=', $max))
            ->when($request->search, fn ($q, $keyword) => $q->search($keyword))
            ->when($request->sort, function ($q, $sort) {
                return match ($sort) {
                    'price_asc' => $q->orderBy('price_sell_idr', 'asc'),
                    'price_desc' => $q->orderBy('price_sell_idr', 'desc'),
                    'mileage_asc' => $q->orderBy('mileage_km', 'asc'),
                    'year_desc' => $q->orderBy('year', 'desc'),
                    default => $q->latest(),
                };
            }, fn ($q) => $q->latest())
            ->with('primaryPhoto')
            ->paginate(12)
            ->withQueryString();

        $brands = CarUnit::query()
            ->publiclyListed()
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('marketplace.index', compact('cars', 'brands'));
    }

    public function show(CarUnit $car): View
    {
        abort_unless($car->isPubliclyListed(), 404);

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

    public function sellCreate(Request $request): View
    {
        $customer = $request->user()->customer;
        $submissions = $customer
            ? CarUnit::where('seller_customer_id', $customer->id)->latest()->get()
            : collect();

        return view('marketplace.sell', compact('submissions'));
    }

    public function sellStore(Request $request, ConsignmentService $consignments): RedirectResponse
    {
        $customer = $request->user()->customer;
        if (! $customer) {
            return back()->with('error', 'Titip jual hanya untuk akun customer.');
        }

        $data = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1990|max:'.(date('Y') + 1),
            'transmission' => 'required|in:manual,automatic,cvt',
            'mileage_km' => 'required|integer|min:0',
            'color' => 'nullable|string|max:30',
            'asking_price_idr' => 'required|integer|min:1000000',
            'condition_defects' => 'required|string|max:2000',
            'condition_additions' => 'nullable|string|max:2000',
            'seller_warranty_info' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        unset($data['photos']);

        $consignments->submit($customer, $data, $request->file('photos', []));

        return redirect()->route('marketplace.sell.create')
            ->with('success', 'Pengajuan titip jual tersimpan. Sales kami akan menghubungi Anda untuk inspeksi.');
    }

    public function sellWithdraw(Request $request, CarUnit $car, ConsignmentService $consignments): RedirectResponse
    {
        $customer = $request->user()->customer;
        abort_unless($customer && $car->isSoldBy($customer), 403);

        try {
            $consignments->withdraw($car);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Unit ditarik dari titip jual.');
    }
}
