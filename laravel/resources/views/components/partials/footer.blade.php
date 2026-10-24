{{--
  x-partials.footer — Public Footer
--}}
<footer class="bg-carbon border-t border-gunmetal mt-auto">
    <div class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ \App\Services\SettingService::getLogo() }}" alt="{{ \App\Services\SettingService::getName() }} Logo" class="w-10 h-10 object-contain drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
                    <h4 class="font-display text-sm text-giallo tracking-wider font-bold">{{ \App\Services\SettingService::getName() }}</h4>
                </div>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    {{ \App\Services\SettingService::getDescription() }}
                </p>
            </div>


            {{-- Services --}}
            <div>
                <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-4">Layanan</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('marketplace.index') }}" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">Marketplace Mobil</a></li>
                    <li><a href="{{ route('workshop.index') }}" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">Bengkel & Servis</a></li>
                    <li><a href="{{ route('parts.index') }}" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">Spare Part</a></li>
                    <li><a href="#" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">AI Sound Scan</a></li>
                </ul>
            </div>


            {{-- Company --}}
            <div>
                <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-4">Perusahaan</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('about') }}" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">Tentang Kami</a></li>
                    <li><a href="{{ route('contact') }}" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">Kontak</a></li>
                    <li><a href="#" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">Karir</a></li>
                    <li><a href="#" class="font-body text-xs text-silver-dim hover:text-giallo transition-premium">FAQ</a></li>
                </ul>
            </div>


            {{-- Contact --}}
            <div>
                <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-4">Kontak</h4>
                <ul class="space-y-2 text-xs text-silver-dim">
                    <li class="flex items-start gap-2">
                        <span class="text-giallo shrink-0">📍</span>
                        <span>Jln. Pitik Wangi Banyumas,<br>Kec. Serpong, Kota Tangerang Selatan,<br>Banten 15311</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-giallo shrink-0">📞</span>
                        <span>+62 812-1845-0622</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-giallo shrink-0">✉️</span>
                        <span>info@audrive.local</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-giallo shrink-0">🕐</span>
                        <span>Sen - Sab: 08:00 - 17:00</span>
                    </li>
                </ul>
            </div>
        </div>


        {{-- Bottom --}}
        <div class="mt-10 pt-6 border-t border-gunmetal/50 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="font-mono text-[10px] text-silver-dim uppercase tracking-wider">
                &copy; {{ date('Y') }} {{ \App\Services\SettingService::getName() }}. All rights reserved.
            </p>
            <div class="flex items-center gap-4">
                <a href="#" class="font-mono text-[10px] text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">Privacy Policy</a>
                <a href="#" class="font-mono text-[10px] text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>



