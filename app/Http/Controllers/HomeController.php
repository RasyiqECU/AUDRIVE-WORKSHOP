<?php


declare(strict_types=1);


namespace App\Http\Controllers;


use App\Models\CarUnit;
use App\Models\SparePartCategory;
use App\Models\Promotion;
use Illuminate\View\View;


class HomeController extends Controller
{
    public function index(): View
    {
        $featuredCars = CarUnit::query()
            ->available()
            ->featured()
            ->with('primaryPhoto')
            ->limit(6)
            ->get();


        $categories = SparePartCategory::query()
            ->ordered()
            ->limit(8)
            ->get();


        $promotions = Promotion::query()
            ->active()
            ->limit(3)
            ->get();


        return view('pages.home', compact('featuredCars', 'categories', 'promotions'));
    }
}



