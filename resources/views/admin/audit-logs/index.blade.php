@extends('layouts.admin')

@section('title', 'Audit Trail & Log Aktivitas Sistem')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-giallo font-mono text-sm tracking-widest uppercase">Tata Kelola & Keamanan</span>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Audit Trail</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                AUDIT TRAIL & LOG SISTEM
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Catatan riwayat seluruh mutasi data, tindakan administratif, dan perubahan status operasional dealer AUDrive.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2">
                <span>🖨️</span> Cetak Log Audit
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Total Catatan Audit</span>
                <span class="text-blue-400">📋</span>
            </div>
            <p class="font-mono text-2xl font-bold text-silver-bright mt-2">
                {{ number_format($totalLogs, 0, ',', '.') }} <span class="text-xs font-normal text-silver-dim">Event</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Tercatat permanen dalam sistem database</p>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Aktivitas Hari Ini</span>
                <span class="text-giallo">⚡</span>
            </div>
            <p class="font-mono text-2xl font-bold text-giallo mt-2">
                {{ number_format($todayLogs, 0, ',', '.') }} <span class="text-xs font-normal text-silver-dim">Tindakan</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Aksi operasional sejak 00:00 hari ini</p>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Staf / User Aktif</span>
                <span class="text-verde">👥</span>
            </div>
            <p class="font-mono text-2xl font-bold text-verde mt-2">
                {{ $users->count() }} <span class="text-xs font-normal text-silver-dim">Akun Internal</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Pemegang hak akses portal administrasi</p>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="panel border border-gunmetal bg-carbon p-4">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search Keyword --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">
                    Cari Deskripsi / Target / IP
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full bg-steel/30 border border-gunmetal rounded p-2 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
            </div>

            {{-- Filter Action --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">
                    Filter Aksi
                </label>
                <select name="action" class="w-full bg-steel/30 border border-gunmetal rounded p-2 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium">
                    <option value="">-- Semua Jenis Aksi --</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $act)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter User --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">
                    Filter Pelaksana (User)
                </label>
                <select name="user_id" class="w-full bg-steel/30 border border-gunmetal rounded p-2 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (string)request('user_id') === (string)$u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ strtoupper($u->role ?? 'user') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-giallo hover:bg-arancio text-carbon font-heading text-xs font-bold uppercase tracking-wider rounded transition-premium">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'action', 'user_id']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-2 bg-steel/40 hover:bg-steel text-silver-dim hover:text-silver-bright font-heading text-xs uppercase rounded transition-premium" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Audit Logs Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pelaksana (User)</th>
                        <th class="py-3 px-4">Aksi & Target</th>
                        <th class="py-3 px-4">Deskripsi Tindakan</th>
                        <th class="py-3 px-4">IP & Device</th>
                        <th class="py-3 px-4 text-right">Data Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    @forelse($logs as $log)
                        @php
                            $actionColors = [
                                'create' => 'bg-verde/20 text-verde border border-verde/40',
                                'created' => 'bg-verde/20 text-verde border border-verde/40',
                                'update' => 'bg-blue-400/20 text-blue-400 border border-blue-400/40',
                                'updated' => 'bg-blue-400/20 text-blue-400 border border-blue-400/40',
                                'delete' => 'bg-danger/20 text-danger border border-danger/40',
                                'deleted' => 'bg-danger/20 text-danger border border-danger/40',
                                'status_change' => 'bg-arancio/20 text-arancio border border-arancio/40',
                                'login' => 'bg-giallo/20 text-giallo border border-giallo/40',
                            ];
                            $actClass = $actionColors[strtolower($log->action)] ?? 'bg-steel/40 text-silver-dim border border-gunmetal';
                            $modelBasename = $log->model_type ? class_basename($log->model_type) : null;
                        @endphp
                        <tr class="hover:bg-steel/10 transition-premium">
                            {{-- Timestamp --}}
                            <td class="py-3.5 px-4 font-mono">
                                <span class="text-silver-bright font-bold block">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="text-giallo text-[11px]">{{ $log->created_at->format('H:i:s') }}</span>
                                <span class="text-silver-dim text-[9px] block">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- User --}}
                            <td class="py-3.5 px-4">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-steel/60 border border-gunmetal flex items-center justify-center font-heading text-[10px] font-bold text-giallo">
                                            {{ substr($log->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-silver-bright">{{ $log->user->name }}</p>
                                            <span class="inline-block text-[9px] font-mono uppercase text-silver-dim">
                                                {{ $log->user->role ?? 'Staff' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono bg-steel/30 text-silver-dim">
                                        SYSTEM / CRON
                                    </span>
                                @endif
                            </td>

                            {{-- Action & Target --}}
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold {{ $actClass }}">
                                    {{ $log->action }}
                                </span>
                                @if($modelBasename)
                                    <span class="block font-mono text-[11px] text-silver-bright mt-1">
                                        {{ $modelBasename }} <span class="text-silver-dim">#{{ $log->model_id }}</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Description --}}
                            <td class="py-3.5 px-4 text-silver-bright">
                                <p class="line-clamp-2 max-w-xs">{{ $log->description ?: '-' }}</p>
                            </td>

                            {{-- IP & Agent --}}
                            <td class="py-3.5 px-4 font-mono text-[11px]">
                                <span class="text-silver-bright block">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                                <span class="text-silver-dim text-[9px] truncate max-w-[140px] block" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?: 'Browser Client', 25) }}
                                </span>
                            </td>

                            {{-- Mutation Data Modal / Details --}}
                            <td class="py-3.5 px-4 text-right">
                                @if(!empty($log->old_values) || !empty($log->new_values))
                                    <details class="relative inline-block text-left">
                                        <summary class="cursor-pointer px-2.5 py-1 bg-steel/40 hover:bg-steel border border-gunmetal rounded font-heading text-[10px] uppercase text-giallo list-none transition-premium">
                                            Detail Nilai ▾
                                        </summary>
                                        <div class="fixed inset-0 bg-carbon/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="this.parentElement.removeAttribute('open')">
                                            <div class="panel border border-gunmetal bg-carbon p-6 max-w-2xl w-full max-h-[85vh] overflow-y-auto space-y-4 text-left shadow-2xl" onclick="event.stopPropagation()">
                                                <div class="flex items-center justify-between border-b border-gunmetal pb-3">
                                                    <div>
                                                        <h4 class="font-heading text-sm font-bold text-silver-bright uppercase">
                                                            Mutasi Data: {{ $log->action }} ({{ $modelBasename }} #{{ $log->model_id }})
                                                        </h4>
                                                        <p class="text-[10px] font-mono text-silver-dim">
                                                            Eksekutor: {{ $log->user->name ?? 'System' }} • {{ $log->created_at->format('d/m/Y H:i:s') }}
                                                        </p>
                                                    </div>
                                                    <button type="button" onclick="this.closest('details').removeAttribute('open')" class="text-silver-dim hover:text-danger text-lg font-bold">
                                                        ✕
                                                    </button>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono">
                                                    {{-- Old Values --}}
                                                    <div class="p-3 bg-steel/20 border border-gunmetal/60 rounded">
                                                        <span class="text-danger font-bold block mb-2 font-heading uppercase text-[10px]">
                                                            - Nilai Sebelumnya (Old Values):
                                                        </span>
                                                        @if(!empty($log->old_values))
                                                            <pre class="whitespace-pre-wrap text-[11px] text-silver-dim overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                        @else
                                                            <p class="text-silver-dim text-[11px] italic">Tidak ada data sebelumnya.</p>
                                                        @endif
                                                    </div>

                                                    {{-- New Values --}}
                                                    <div class="p-3 bg-steel/20 border border-gunmetal/60 rounded">
                                                        <span class="text-verde font-bold block mb-2 font-heading uppercase text-[10px]">
                                                            + Nilai Baru (New Values):
                                                        </span>
                                                        @if(!empty($log->new_values))
                                                            <pre class="whitespace-pre-wrap text-[11px] text-silver-bright overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                        @else
                                                            <p class="text-silver-dim text-[11px] italic">Tidak ada data baru.</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="text-right pt-3 border-t border-gunmetal">
                                                    <button type="button" onclick="this.closest('details').removeAttribute('open')" class="px-4 py-1.5 bg-steel/50 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded transition-premium">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-silver-dim text-[10px] font-mono">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-silver-dim">
                                <span class="text-3xl block mb-2">📋</span>
                                <p class="font-heading uppercase text-xs">Belum Ada Rekaman Audit Log</p>
                                <p class="text-[11px] text-silver-dim mt-1">Seluruh aktivitas sistem dan mutasi data akan dicatat secara otomatis di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-4 border-t border-gunmetal">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
