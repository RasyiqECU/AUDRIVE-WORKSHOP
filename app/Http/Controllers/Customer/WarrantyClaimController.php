<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyClaimController extends Controller
{
    public function index(Request $request): View
    {
        $claims = WarrantyClaim::where('customer_id', $request->user()->customer?->id ?? 0)
            ->with(['vehicle', 'repairWorkOrder'])
            ->latest()
            ->paginate(10);

        return view('customer.warranty.index', compact('claims'));
    }
}
