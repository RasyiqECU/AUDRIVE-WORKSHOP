<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Services\SparePartService;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class InventoryController extends Controller
{
    public function __construct(
        protected SparePartService $sparePartService,
        protected StockService $stockService
    ) {}


    public function index(Request $request): View
    {
        $parts = SparePart::with('category')
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->low_stock, fn($q) => $q->whereColumn('stock', '<=', 'low_stock_threshold'))
            ->orderBy('name')
            ->paginate(20);


        $categories = SparePartCategory::orderBy('name')->get();
        $lowStockCount = SparePart::whereColumn('stock', '<=', 'low_stock_threshold')->count();


        return view('admin.inventory.index', compact('parts', 'categories', 'lowStockCount'));
    }


    public function create(): View
    {
        $categories = SparePartCategory::orderBy('name')->get();
        return view('admin.inventory.create', compact('categories'));
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


        return redirect()->route('admin.inventory.index')
            ->with('success', 'Spare part berhasil ditambahkan.');
    }


    public function edit(SparePart $sparePart): View
    {
        $categories = SparePartCategory::orderBy('name')->get();
        $sparePart->load('compatibilities');
        return view('admin.inventory.edit', compact('sparePart', 'categories'));
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


        return redirect()->route('admin.inventory.index')
            ->with('success', 'Spare part berhasil diperbarui.');
    }


    public function adjustStock(Request $request, SparePart $sparePart): RedirectResponse
    {
        $request->validate([
            'new_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);


        try {
            $this->stockService->adjustStock($sparePart, $request->new_stock, $request->notes);
            return back()->with('success', 'Stok berhasil disesuaikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function movements(SparePart $sparePart): View
    {
        $movements = $this->stockService->getStockMovements($sparePart);
        return view('admin.inventory.movements', compact('sparePart', 'movements'));
    }


    public function lowStock(): View
    {
        $parts = $this->sparePartService->getLowStockParts();
        return view('admin.inventory.low-stock', compact('parts'));
    }
}



