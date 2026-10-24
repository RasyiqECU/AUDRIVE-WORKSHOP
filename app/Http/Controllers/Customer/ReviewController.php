<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $reviews = Review::where('reviewer_type', Customer::class)
            ->where('reviewer_id', $user->customer?->id ?? 0)
            ->latest()
            ->paginate(10);

        return view('customer.reviews.index', compact('reviews'));
    }

    public function create(Request $request): View
    {
        return view('customer.reviews.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'reviewable_type' => 'nullable|string',
            'reviewable_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();

        Review::create([
            'reviewer_type' => Customer::class,
            'reviewer_id' => $customer->id,
            'reviewable_type' => $validated['reviewable_type'] ?? Customer::class,
            'reviewable_id' => $validated['reviewable_id'] ?? $customer->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'approved',
        ]);

        return redirect()->route('customer.reviews.index')->with('success', 'Ulasan Anda berhasil dikirim.');
    }
}
