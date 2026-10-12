@extends('layouts.admin')

@section('title', 'Detail Pengguna — ' . $user->name)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Kembali ke Daftar Pengguna</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-1.5 bg-giallo text-nero font-heading font-bold text-xs uppercase rounded">✏️ Edit Pengguna</a>
    </div>

    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <div class="flex items-center gap-4 border-b border-gunmetal pb-4">
            <div class="w-14 h-14 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center font-display font-bold text-2xl text-giallo">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="font-display text-2xl font-bold text-silver-bright">{{ $user->name }}</h1>
                <p class="font-mono text-xs text-silver-dim">{{ $user->email }} &bull; {{ $user->phone ?: 'No phone' }}</p>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-4 text-xs font-mono">
            <div>
                <dt class="text-silver-dim">Role</dt>
                <dd class="text-silver-bright font-bold uppercase mt-0.5">{{ $user->role }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim">Status Akun</dt>
                <dd class="mt-0.5">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim">Email Terverifikasi</dt>
                <dd class="mt-0.5 text-silver-bright">{{ $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Belum' }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim">Terdaftar Sejak</dt>
                <dd class="mt-0.5 text-silver-bright">{{ $user->created_at->format('d M Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
