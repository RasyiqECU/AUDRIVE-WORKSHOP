<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Voucher::latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where(function ($q) {
                          $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                      })
                      ->whereColumn('used_count', '<', 'max_usage');
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<=', now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->paginate(15)->withQueryString();

        $stats = [
            'total'          => Voucher::count(),
            'active'         => Voucher::where('is_active', true)->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(),
            'loyalty_reward' => Voucher::where('points_cost', '>', 0)->count(),
            'total_used'     => Voucher::sum('used_count'),
        ];

        return view('admin.vouchers.index', compact('vouchers', 'stats'));
    }

    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:vouchers,code',
            'type'             => 'required|string|in:fixed,percent',
            'value'            => 'required|numeric|min:1',
            'points_cost'      => 'nullable|numeric|min:0',
            'min_purchase_idr' => 'nullable|numeric|min:0',
            'max_usage'        => 'required|integer|min:1',
            'expires_at'       => 'nullable|date|after:today',
            'is_active'        => 'nullable|boolean',
            'description'      => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['points_cost'] = (int) ($validated['points_cost'] ?? 0);
        $validated['min_purchase_idr'] = (int) ($validated['min_purchase_idr'] ?? 0);
        $validated['is_active'] = $request->has('is_active');

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')->with('success', "Voucher '{$validated['code']}' berhasil ditambahkan.");
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code'             => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($voucher->id)],
            'type'             => 'required|string|in:fixed,percent',
            'value'            => 'required|numeric|min:1',
            'points_cost'      => 'nullable|numeric|min:0',
            'min_purchase_idr' => 'nullable|numeric|min:0',
            'max_usage'        => 'required|integer|min:1',
            'expires_at'       => 'nullable|date',
            'is_active'        => 'nullable|boolean',
            'description'      => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['points_cost'] = (int) ($validated['points_cost'] ?? 0);
        $validated['min_purchase_idr'] = (int) ($validated['min_purchase_idr'] ?? 0);
        $validated['is_active'] = $request->has('is_active');

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', "Voucher '{$voucher->code}' berhasil diperbarui.");
    }

    public function toggle(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Status voucher '{$voucher->code}' berhasil {$status}.");
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        if ($voucher->used_count > 0) {
            $voucher->update(['is_active' => false]);
            return back()->with('info', "Voucher '{$voucher->code}' sudah pernah digunakan, sehingga statusnya dinonaktifkan alih-alih dihapus permanen.");
        }

        $voucher->delete();
        return back()->with('success', "Voucher '{$voucher->code}' berhasil dihapus.");
    }
}
