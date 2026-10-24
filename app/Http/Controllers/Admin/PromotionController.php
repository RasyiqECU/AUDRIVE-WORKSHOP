<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::latest()->paginate(15);

        $stats = [
            'total'    => Promotion::count(),
            'active'   => Promotion::where('is_active', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now())->count(),
            'upcoming' => Promotion::where('starts_at', '>', now())->count(),
        ];

        return view('admin.promotions.index', compact('promotions', 'stats'));
    }

    public function create(): View
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'type'           => 'required|string|in:fixed,percent',
            'value'          => 'required|numeric|min:1',
            'starts_at'      => 'required|date',
            'ends_at'        => 'required|date|after_or_equal:starts_at',
            'applicable_to'  => 'nullable|string|max:30',
            'description'    => 'nullable|string|max:2000',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_active'] = $request->has('is_active');
        $validated['applicable_to'] = $validated['applicable_to'] ?: 'Semua Layanan';

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')->with('success', "Kampanye Promo '{$validated['name']}' berhasil dipublikasikan.");
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'type'           => 'required|string|in:fixed,percent',
            'value'          => 'required|numeric|min:1',
            'starts_at'      => 'required|date',
            'ends_at'        => 'required|date|after_or_equal:starts_at',
            'applicable_to'  => 'nullable|string|max:30',
            'description'    => 'nullable|string|max:2000',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['applicable_to'] = $validated['applicable_to'] ?: 'Semua Layanan';

        $promotion->update($validated);

        return redirect()->route('admin.promotions.index')->with('success', "Promo '{$promotion->name}' berhasil diperbarui.");
    }

    public function toggle(Promotion $promotion): RedirectResponse
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        $status = $promotion->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status promo '{$promotion->name}' berhasil {$status}.");
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();
        return back()->with('success', "Promo '{$promotion->name}' berhasil dihapus.");
    }
}
