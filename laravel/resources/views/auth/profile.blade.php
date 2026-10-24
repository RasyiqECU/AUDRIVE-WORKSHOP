@extends('layouts.app')


@section('title', 'Profil Saya')


@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 mb-2">
            <div class="h-px w-8 bg-giallo"></div>
            <span class="font-heading text-xs uppercase tracking-[0.3em] text-giallo">Akun</span>
        </div>
        <h1 class="font-display text-2xl font-bold text-silver-bright tracking-wide">Profil Saya</h1>
        <p class="text-sm text-silver-dim mt-2">Kelola informasi akun dan password Anda.</p>
    </div>


    {{-- Success Message --}}
    @if (session('status'))
        <div class="bg-verde/10 border border-verde/30 p-4 text-sm text-verde">
            {{ session('status') }}
        </div>
    @endif


    {{-- Profile Information --}}
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal">
            <h2 class="font-heading text-sm font-semibold text-silver-bright">Informasi Profil</h2>
            <p class="font-body text-xs text-silver-dim mt-1">Perbarui informasi akun dan alamat email Anda.</p>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PATCH')


            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                    @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>


                <div>
                    <label for="email" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                    @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>


                <div>
                    <label for="phone" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Nomor Telepon</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                    @error('phone') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>


                <div>
                    <label for="birth_date" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Tanggal Lahir</label>
                    <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date', $customer?->birth_date?->format('Y-m-d')) }}"
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                </div>
            </div>


            <div>
                <label for="address" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Alamat</label>
                <textarea id="address" name="address" rows="2"
                    class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium">{{ old('address', $customer?->address) }}</textarea>
            </div>


            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label for="city" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Kota</label>
                    <input id="city" type="text" name="city" value="{{ old('city', $customer?->city) }}"
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                </div>


                <div>
                    <label for="province" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Provinsi</label>
                    <input id="province" type="text" name="province" value="{{ old('province', $customer?->province) }}"
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                </div>
            </div>


            <div class="flex justify-end pt-4 border-t border-gunmetal/50">
                <button type="submit" class="bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider px-6 py-2.5 hover:bg-arancio transition-premium clip-angular-button">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>


    {{-- Update Password --}}
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal">
            <h2 class="font-heading text-sm font-semibold text-silver-bright">Ubah Password</h2>
            <p class="font-body text-xs text-silver-dim mt-1">Pastikan akun Anda menggunakan password yang panjang dan acak.</p>
        </div>
        <form method="POST" action="{{ route('password.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')


            <div>
                <label for="current_password" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Password Saat Ini</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                    class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                @error('current_password') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>


            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Password Baru</label>
                    <input id="password" type="password" name="password" autocomplete="new-password"
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                    @error('password') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>


                <div>
                    <label for="password_confirmation" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium" />
                </div>
            </div>


            <div class="flex justify-end pt-4 border-t border-gunmetal/50">
                <button type="submit" class="bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider px-6 py-2.5 hover:bg-arancio transition-premium clip-angular-button">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



