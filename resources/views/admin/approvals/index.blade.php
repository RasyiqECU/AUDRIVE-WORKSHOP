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

    {{-- Panduan Penggunaan Pusat Approval --}}
    <div class="panel border border-giallo/30 bg-giallo/5 p-5">
        <h2 class="font-heading text-xs uppercase tracking-[0.2em] text-giallo flex items-center gap-2">
            📘 Tata Cara Penggunaan Pusat Approval
        </h2>
        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-silver-bright leading-relaxed">
            <div class="space-y-2">
                <p><strong class="text-giallo">1. Apa fungsinya?</strong><br>
                Pusat Approval adalah meja cerdas pembuat keputusan. Staf operasional (Sales, Kasir, Inventory) <em>tidak boleh</em> memutuskan hal berdampak finansial sendirian: mereka <strong>mengajukan</strong>, Anda (Manager) <strong>menyetujui atau menolak</strong>. Ini menjaga prinsip pemisahan tugas: pembuat pengajuan ≠ penyetuju.</p>
                <p><strong class="text-giallo">2. Anda menerima pengajuan apa?</strong>
                </p>
                <ul class="list-disc list-inside text-silver-dim space-y-1 pl-1">
                    <li><strong class="text-silver-bright">discount</strong> — permohonan diskon transaksi di atas wewenang staf.</li>
                    <li><strong class="text-silver-bright">refund</strong> — permohonan pengembalian dana (void/refund transaksi kasir).</li>
                    <li><strong class="text-silver-bright">price_adjustment</strong> — koreksi harga jual di Sales Order/katalog.</li>
                    <li><strong class="text-silver-bright">stock_adjustment</strong> — selisih besar stok gudang yang diajukan Inventory.</li>
                </ul>
            </div>
            <div class="space-y-2">
                <p><strong class="text-giallo">3. Alur kerja harian Anda</strong></p>
                <ol class="list-decimal list-inside text-silver-dim space-y-1 pl-1">
                    <li>Buka menu <strong class="text-silver-bright">✅ Pusat Approval</strong> (atau klik notifikasi "Approval Requested" yang masuk ke Anda).</li>
                    <li>Filter berdasarkan <strong class="text-silver-bright">Status = Pending</strong> untuk melihat antrean pengajuan.</li>
                    <li>Klik <strong class="text-silver-bright">Detail</strong> untuk membaca payload: siapa pemohon, jenis pengajuan, nilai/nominal, dan referensinya.</li>
                    <li>Putuskan: setujui atau tolak dengan catatan/reason (opsi tersedia di halaman detail).</li>
                    <li>Sistem mencatat keputusan Anda di <strong class="text-silver-bright">Audit Trail</strong> dan mengirim notifikasi balik ke pengaju.</li>
                </ol>
                <p><strong class="text-giallo">4. Yang perlu diingat</strong><br>
                <span class="text-silver-dim">Status pengajuan juga bisa dipantau oleh staf di modul masing-masing; setiap keputusan bersifat final untuk pengajuan tersebut (tidak bisa diubah ulang — harus mengajukan baru). Penyetujuan langsung mengeksekusi aksinya ke sistem.</span></p>
            </div>
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



