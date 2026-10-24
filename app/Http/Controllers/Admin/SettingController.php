<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected ?AuditService $auditService = null
    ) {}

    /**
     * Tampilkan halaman pengaturan sistem & branding (Khusus Super Admin).
     */
    public function index(Request $request): View
    {
        if ($request->user()?->role !== 'super-admin') {
            abort(403, 'Akses ditolak. Pengaturan sistem hanya dapat diakses oleh Super Admin.');
        }

        $settings = [
            'app_name'             => SettingService::getName(),
            'app_tagline'          => SettingService::getTagline(),
            'app_description'      => SettingService::getDescription(),
            'app_logo'             => SettingService::getLogo(),
            'login_bg'             => SettingService::getLoginBackground(),
            'hero_bg'              => SettingService::getHeroBackground(),
            'dashboard_bg'         => SettingService::getDashboardBackground(),
            'workshop_bg'          => SettingService::getWorkshopBackground(),
            'marketplace_bg'       => SettingService::getMarketplaceBackground(),
            'sparepart_bg'         => SettingService::getSparepartBackground(),
            'about_bg'             => SettingService::getAboutBackground(),
            'contact_bg'           => SettingService::getContactBackground(),
            'custom_logo'          => SettingService::get('app_logo'),
            'custom_login_bg'      => SettingService::get('login_bg'),
            'custom_hero_bg'       => SettingService::get('hero_bg'),
            'custom_dashboard_bg'  => SettingService::get('dashboard_bg'),
            'custom_workshop_bg'   => SettingService::get('workshop_bg'),
            'custom_marketplace_bg'=> SettingService::get('marketplace_bg'),
            'custom_sparepart_bg'  => SettingService::get('sparepart_bg'),
            'custom_about_bg'      => SettingService::get('about_bg'),
            'custom_contact_bg'    => SettingService::get('contact_bg'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Simpan pembaruan pengaturan dan file branding yang diunggah.
     */
    public function update(Request $request): RedirectResponse
    {
        if ($request->user()?->role !== 'super-admin') {
            abort(403, 'Akses ditolak. Pengaturan sistem hanya dapat diakses oleh Super Admin.');
        }

        $validated = $request->validate([
            'app_name'           => ['nullable', 'string', 'max:100'],
            'app_tagline'        => ['nullable', 'string', 'max:200'],
            'app_description'    => ['nullable', 'string', 'max:500'],
            'logo_file'          => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,gif', 'max:8192'],
            'login_bg_file'      => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'hero_bg_file'       => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'dashboard_bg_file'  => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'workshop_bg_file'   => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'marketplace_bg_file'=> ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'sparepart_bg_file'  => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'about_bg_file'      => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'contact_bg_file'    => ['nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp,mp4', 'max:30720'],
            'sync_hero_bg'       => ['nullable', 'boolean'],
        ], [
            'logo_file.mimes'           => 'Logo harus berformat PNG, JPG, JPEG, WEBP, SVG, atau GIF.',
            'logo_file.max'             => 'Ukuran file logo maksimal 8 MB.',
            'login_bg_file.mimes'       => 'Background login harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'login_bg_file.max'         => 'Ukuran file background login maksimal 30 MB.',
            'hero_bg_file.mimes'        => 'Hero background harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'hero_bg_file.max'          => 'Ukuran file hero background maksimal 30 MB.',
            'dashboard_bg_file.mimes'   => 'Background dashboard harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'dashboard_bg_file.max'     => 'Ukuran file background dashboard maksimal 30 MB.',
            'workshop_bg_file.mimes'    => 'Background bengkel harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'workshop_bg_file.max'      => 'Ukuran file background bengkel maksimal 30 MB.',
            'marketplace_bg_file.mimes' => 'Background marketplace harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'marketplace_bg_file.max'   => 'Ukuran file background marketplace maksimal 30 MB.',
            'sparepart_bg_file.mimes'   => 'Background sparepart harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'sparepart_bg_file.max'     => 'Ukuran file background sparepart maksimal 30 MB.',
            'about_bg_file.mimes'       => 'Background tentang kami harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'about_bg_file.max'         => 'Ukuran file background tentang kami maksimal 30 MB.',
            'contact_bg_file.mimes'     => 'Background kontak harus berupa file GIF, JPG, JPEG, PNG, WEBP, atau MP4.',
            'contact_bg_file.max'       => 'Ukuran file background kontak maksimal 30 MB.',
        ]);

        $textUpdates = [];
        if (isset($validated['app_name'])) {
            $textUpdates['app_name'] = trim($validated['app_name']);
        }
        if (isset($validated['app_tagline'])) {
            $textUpdates['app_tagline'] = trim($validated['app_tagline']);
        }
        if (isset($validated['app_description'])) {
            $textUpdates['app_description'] = trim($validated['app_description']);
        }

        if (!empty($textUpdates)) {
            SettingService::setMany($textUpdates);
        }

        // Upload Logo Baru jika ada
        if ($request->hasFile('logo_file')) {
            SettingService::uploadFile($request->file('logo_file'), 'app_logo');
        }

        // Upload GIF / Foto Background Halaman Sebelum Login jika ada
        if ($request->hasFile('login_bg_file')) {
            $loginBgUrl = SettingService::uploadFile($request->file('login_bg_file'), 'login_bg');

            // Jika opsi sinkronisasi ke hero landing page dicentang
            if ($request->boolean('sync_hero_bg')) {
                SettingService::set('hero_bg', $loginBgUrl);
            }
        }

        // Upload GIF / Foto Hero Background Halaman Utama jika ada file terpisah
        if ($request->hasFile('hero_bg_file')) {
            SettingService::uploadFile($request->file('hero_bg_file'), 'hero_bg');
        }

        // Upload GIF / Foto Dashboard Banner
        if ($request->hasFile('dashboard_bg_file')) {
            SettingService::uploadFile($request->file('dashboard_bg_file'), 'dashboard_bg');
        }

        // Upload GIF / Foto Halaman Bengkel
        if ($request->hasFile('workshop_bg_file')) {
            SettingService::uploadFile($request->file('workshop_bg_file'), 'workshop_bg');
        }

        // Upload GIF / Foto Halaman Marketplace Showroom
        if ($request->hasFile('marketplace_bg_file')) {
            SettingService::uploadFile($request->file('marketplace_bg_file'), 'marketplace_bg');
        }

        // Upload GIF / Foto Halaman Sparepart
        if ($request->hasFile('sparepart_bg_file')) {
            SettingService::uploadFile($request->file('sparepart_bg_file'), 'sparepart_bg');
        }

        // Upload GIF / Foto Halaman Tentang Kami
        if ($request->hasFile('about_bg_file')) {
            SettingService::uploadFile($request->file('about_bg_file'), 'about_bg');
        }

        // Upload GIF / Foto Halaman Kontak
        if ($request->hasFile('contact_bg_file')) {
            SettingService::uploadFile($request->file('contact_bg_file'), 'contact_bg');
        }

        // Catat Audit Trail
        try {
            $this->auditService?->log(
                action: 'UPDATE_BRANDING_SETTINGS',
                modelType: 'Setting',
                description: 'Super Admin memperbarui konfigurasi visual media dan background halaman sistem AUDrive.'
            );
        } catch (\Throwable) {
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Tampilan dan media background aplikasi berhasil diperbarui! Perubahan langsung aktif di seluruh halaman.');
    }

    /**
     * Reset item tertentu kembali ke pengaturan default bawaan.
     */
    public function reset(Request $request): RedirectResponse
    {
        if ($request->user()?->role !== 'super-admin') {
            abort(403, 'Akses ditolak.');
        }

        $item = $request->input('item');

        match ($item) {
            'logo'           => SettingService::resetItem('app_logo'),
            'login_bg'       => SettingService::resetItem('login_bg'),
            'hero_bg'        => SettingService::resetItem('hero_bg'),
            'dashboard_bg'   => SettingService::resetItem('dashboard_bg'),
            'workshop_bg'    => SettingService::resetItem('workshop_bg'),
            'marketplace_bg' => SettingService::resetItem('marketplace_bg'),
            'sparepart_bg'   => SettingService::resetItem('sparepart_bg'),
            'about_bg'       => SettingService::resetItem('about_bg'),
            'contact_bg'     => SettingService::resetItem('contact_bg'),
            'name'           => SettingService::set('app_name', SettingService::DEFAULT_APP_NAME),
            'tagline'        => SettingService::set('app_tagline', SettingService::DEFAULT_TAGLINE),
            'all'            => (function () {
                SettingService::resetItem('app_logo');
                SettingService::resetItem('login_bg');
                SettingService::resetItem('hero_bg');
                SettingService::resetItem('dashboard_bg');
                SettingService::resetItem('workshop_bg');
                SettingService::resetItem('marketplace_bg');
                SettingService::resetItem('sparepart_bg');
                SettingService::resetItem('about_bg');
                SettingService::resetItem('contact_bg');
                SettingService::set('app_name', SettingService::DEFAULT_APP_NAME);
                SettingService::set('app_tagline', SettingService::DEFAULT_TAGLINE);
                SettingService::set('app_description', SettingService::DEFAULT_DESCRIPTION);
            })(),
            default          => null,
        };

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Item berhasil direset kembali ke default bawaan sistem.');
    }
}
