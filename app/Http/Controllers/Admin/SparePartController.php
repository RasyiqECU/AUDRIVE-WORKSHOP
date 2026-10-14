<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Services\SparePartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SparePartController extends Controller
{
    public function __construct(
        protected SparePartService $sparePartService
    ) {}

    public function index(Request $request): View
    {
        $spareParts = SparePart::with('category')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('name', 'like', "%{$s}%")
                        ->orWhere('sku', 'like', "%{$s}%")
                        ->orWhere('brand', 'like', "%{$s}%");
                });
            })
            ->when($request->category_id, fn($q, $cat) => $q->where('category_id', $cat))
            ->latest()
            ->paginate(15);

        $categories = SparePartCategory::orderBy('name')->get();

        return view('admin.spare-parts.index', compact('spareParts', 'categories'));
    }

    public function show(SparePart $sparePart): View
    {
        $sparePart->load(['category', 'compatibilities', 'stockMovements' => fn($q) => $q->latest()->take(10)]);
        return view('admin.spare-parts.show', compact('sparePart'));
    }

    public function create(): View
    {
        $categories = SparePartCategory::orderBy('name')->get();
        return view('admin.spare-parts.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:spare_parts,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:spare_part_categories,id',
            'brand' => 'nullable|string|max:100',
            'price_idr' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $this->sparePartService->create($validated);

        return redirect()->route('admin.spare-parts.index')->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function edit(SparePart $sparePart): View
    {
        $categories = SparePartCategory::orderBy('name')->get();
        $sparePart->load('compatibilities');
        return view('admin.spare-parts.edit', compact('sparePart', 'categories'));
    }

    public function update(Request $request, SparePart $sparePart): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:spare_parts,sku,' . $sparePart->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:spare_part_categories,id',
            'brand' => 'nullable|string|max:100',
            'price_idr' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $this->sparePartService->update($sparePart, $validated);

        return redirect()->route('admin.spare-parts.index')->with('success', 'Spare part berhasil diperbarui.');
    }

    public function destroy(SparePart $sparePart): RedirectResponse
    {
        $sparePart->delete();
        return redirect()->route('admin.spare-parts.index')->with('success', 'Spare part berhasil dihapus.');
    }
}
