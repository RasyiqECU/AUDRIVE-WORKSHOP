<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use Illuminate\View\View;

class InspectionController extends Controller
{
    public function show(CarUnit $car): View
    {
        $car->load(['latestInspection.inspector', 'latestInspection.aiScan', 'primaryPhoto']);
        $inspection = $car->latestInspection;

        return view('marketplace.inspection', compact('car', 'inspection'));
    }
}
