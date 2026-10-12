<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SparePartController extends Controller
{
    public function index(Request $request): View
    {
        $spareParts = SparePart::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('part_number', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15);

        return view('admin.spare-parts.index', compact('spareParts'));
    }

    public function show(SparePart $sparePart): View
    {
        return view('admin.spare-parts.show', compact('sparePart'));
    }

    public function create(): View
    {
        return view('admin.spare-parts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'part_number' => 'required|string|max:50|unique:spare_parts,part_number',
            'name' => 'required|string|max:100',
            'brand' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        SparePart::create($validated);

        return redirect()->route('admin.spare-parts.index')->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function edit(SparePart $sparePart): View
    {
        return view('admin.spare-parts.edit', compact('sparePart'));
    }

    public function update(Request $request, SparePart $sparePart): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'brand' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $sparePart->update($validated);

        return redirect()->route('admin.spare-parts.index')->with('success', 'Spare part berhasil diperbarui.');
    }

    public function destroy(SparePart $sparePart): RedirectResponse
    {
        $sparePart->delete();
        return redirect()->route('admin.spare-parts.index')->with('success', 'Spare part berhasil dihapus.');
    }
}
