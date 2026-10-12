@extends('layouts.admin')


@section('title', 'Approval Requests')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-silver-bright tracking-wide">
                Approval Requests
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Kelola permintaan approval untuk diskon, refund, dan adjustment
            </p>
        </div>
    </div>


    {{-- Filters --}}
    <div class="panel p-4 border border-gunmetal">
        <form method="GET" action="{{ route('admin.approvals.index') }}" class="flex flex-wrap gap-3">
            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>


            <select name="type" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Tipe</option>
                <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>Discount</option>
                <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Refund</option>
                <option value="price_adjustment" {{ request('type') === 'price_adjustment' ? 'selected' : '' }}>Price Adjustment</option>
                <option value="stock_adjustment" {{ request('type') === 'stock_adjustment' ? 'selected' : '' }}>Stock Adjustment</option>
            </select>


            <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase tracking-wider hover:bg-arancio transition-premium">
                Filter
            </button>


            <a href="{{ route('admin.approvals.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
                Reset
            </a>
        </form>
    </div>


    {{-- Approvals Table --}}
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gunmetal bg-steel/20">
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">ID</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tipe</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Pemohon</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tanggal</th>
                        <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Disetujui Oleh</th>
                        <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    @forelse($approvals as $approval)
                    <tr class="hover:bg-steel/20 transition-premium">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-giallo">#{{ $approval->id }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-heading text-xs text-silver-bright uppercase">
                                {{ str_replace('_', ' ', $approval->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-body text-xs text-silver-bright">{{ $approval->requester->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-silver-dim">
                                {{ $approval->created_at->format('d M Y H:i') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($approval->status === 'pending')
                                <span class="px-2 py-1 bg-giallo/10 text-giallo font-heading text-[10px] uppercase tracking-wider">
                                    Pending
                                </span>
                            @elseif($approval->status === 'approved')
                                <span class="px-2 py-1 bg-verde/10 text-verde font-heading text-[10px] uppercase tracking-wider">
                                    Approved
                                </span>
                            @else
                                <span class="px-2 py-1 bg-danger/10 text-danger font-heading text-[10px] uppercase tracking-wider">
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($approval->approver)
                                <span class="font-body text-xs text-silver-bright">{{ $approval->approver->name }}</span>
                            @else
                                <span class="font-body text-xs text-silver-dim">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.approvals.show', $approval) }}"
                               class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center">
                            <p class="font-body text-sm text-silver-dim">Tidak ada approval request</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- Pagination --}}
    @if($approvals->hasPages())
    <div class="flex justify-center">
        {{ $approvals->links() }}
    </div>
    @endif
</div>
@endsection



