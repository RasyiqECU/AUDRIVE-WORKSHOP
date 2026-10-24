<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public const CACHE_KEY = 'audrive_site_settings';
    public const CACHE_TTL = 86400; // 24 hours

    public const DEFAULT_APP_NAME = 'AUDRIVE';
    public const DEFAULT_TAGLINE = 'PREMIUM AUTOMOTIVE DEALER';
    public const DEFAULT_DESCRIPTION = 'Dealer mobil premium dengan layanan bengkel berstandar tinggi dan teknologi AI untuk diagnosis suara mesin.';
    public const DEFAULT_LOGO = '/images/AUDrive-logo.png';
    public const DEFAULT_LOGIN_BG = '/images/lambo.gif';
    public const DEFAULT_HERO_BG = '/images/lambo.gif';
    public const DEFAULT_DASHBOARD_BG = '/images/lambo.gif';
    public const DEFAULT_WORKSHOP_BG = '/images/bengkel.gif';
    public const DEFAULT_MARKETPLACE_BG = '/images/marketplace.gif';
    public const DEFAULT_SPAREPART_BG = '/images/sparepart.gif';
    public const DEFAULT_ABOUT_BG = '/images/tentang.jpeg';
    public const DEFAULT_CONTACT_BG = '/images/kontak.jpg';

    /**
     * Get all settings from cache, database, or fallback JSON file.
     */
    public static function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $settings = [];

            // 1. Try reading from Database
            try {
                if (Schema::hasTable('settings')) {
                    $dbSettings = Setting::all()->pluck('value', 'key')->toArray();
                    $settings = array_merge($settings, $dbSettings);
                }
            } catch (\Throwable) {
                // Database may be offline or not migrated yet
            }

            // 2. Read / merge with persistent JSON storage
            $jsonFile = storage_path('app/settings.json');
            if (File::exists($jsonFile)) {
                $jsonContent = json_decode(File::get($jsonFile), true);
                if (is_array($jsonContent)) {
                    $settings = array_merge($jsonContent, $settings);
                }
            }

            return $settings;
        });
    }

    /**
     * Retrieve a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::all();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a single setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        self::setMany([$key => $value]);
    }

    /**
     * Persist multiple settings into both JSON and database.
     */
    public static function setMany(array $data): void
    {
        // 1. Write to JSON file for high resilience (works even when DB is offline)
        $jsonFile = storage_path('app/settings.json');
        $existing = [];
        if (File::exists($jsonFile)) {
            $existing = json_decode(File::get($jsonFile), true) ?: [];
        }
        $merged = array_merge($existing, $data);
        File::ensureDirectoryExists(dirname($jsonFile));
        File::put($jsonFile, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // 2. Write to Database if table exists
        try {
            if (Schema::hasTable('settings')) {
                foreach ($data as $key => $value) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => is_scalar($value) || is_null($value) ? (string) $value : json_encode($value)]
                    );
                }
            }
        } catch (\Throwable) {
            // Ignore DB errors safely
        }

        // 3. Clear cache
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Upload branding file (logo or background gif/image).
     */
    public static function uploadFile(UploadedFile $file, string $settingKey, string $folder = 'branding'): string
    {
        // Clean up previous uploaded custom file if exists
        self::deletePreviousUpload($settingKey);

        $extension = $file->getClientOriginalExtension();
        $filename = $settingKey . '_' . time() . '.' . $extension;

        // Store to public disk
        $path = $file->storeAs($folder, $filename, 'public');
        $url = asset('storage/' . $path);

        self::set($settingKey, $url);

        return $url;
    }

    /**
     * Delete previous uploaded file for a setting key.
     */
    protected static function deletePreviousUpload(string $settingKey): void
    {
        $oldUrl = self::get($settingKey);
        if ($oldUrl && str_contains($oldUrl, '/storage/branding/')) {
            $parsed = parse_url($oldUrl, PHP_URL_PATH);
            $relativePath = preg_replace('#^.*?/storage/#', '', $parsed);
            try {
                Storage::disk('public')->delete($relativePath);
            } catch (\Throwable) {
            }
        }
    }

    /**
     * Reset a setting key to default by removing its custom value.
     */
    public static function resetItem(string $settingKey): void
    {
        self::deletePreviousUpload($settingKey);
        self::set($settingKey, null);
    }

    /**
     * Helper to get application name.
     */
    public static function getName(): string
    {
        return (string) self::get('app_name', self::DEFAULT_APP_NAME);
    }

    /**
     * Helper to get application tagline.
     */
    public static function getTagline(): string
    {
        return (string) self::get('app_tagline', self::DEFAULT_TAGLINE);
    }

    /**
     * Helper to get application description.
     */
    public static function getDescription(): string
    {
        return (string) self::get('app_description', self::DEFAULT_DESCRIPTION);
    }

    /**
     * Helper to get application logo URL.
     */
    public static function getLogo(): string
    {
        $custom = self::get('app_logo');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_LOGO, '/'));
    }

    /**
     * Helper to get login page (halaman sebelum login) background GIF/image URL.
     */
    public static function getLoginBackground(): string
    {
        $custom = self::get('login_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_LOGIN_BG, '/'));
    }

    /**
     * Helper to get home landing page hero background GIF/image URL.
     */
    public static function getHeroBackground(): string
    {
        $custom = self::get('hero_bg');
        if (!empty($custom)) {
            return $custom;
        }

        $loginBg = self::get('login_bg');
        if (!empty($loginBg)) {
            return $loginBg;
        }

        return asset(ltrim(self::DEFAULT_HERO_BG, '/'));
    }

    /**
     * Helper to get dashboard hero background image/GIF.
     */
    public static function getDashboardBackground(): string
    {
        $custom = self::get('dashboard_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_DASHBOARD_BG, '/'));
    }

    /**
     * Helper to get workshop (bengkel) hero GIF/image.
     */
    public static function getWorkshopBackground(): string
    {
        $custom = self::get('workshop_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_WORKSHOP_BG, '/'));
    }

    /**
     * Helper to get marketplace (showroom) hero GIF/image.
     */
    public static function getMarketplaceBackground(): string
    {
        $custom = self::get('marketplace_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_MARKETPLACE_BG, '/'));
    }

    /**
     * Helper to get sparepart (suku cadang) hero GIF/image.
     */
    public static function getSparepartBackground(): string
    {
        $custom = self::get('sparepart_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_SPAREPART_BG, '/'));
    }

    /**
     * Helper to get about (tentang kami) hero image/GIF.
     */
    public static function getAboutBackground(): string
    {
        $custom = self::get('about_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_ABOUT_BG, '/'));
    }

    /**
     * Helper to get contact (kontak) hero image/GIF.
     */
    public static function getContactBackground(): string
    {
        $custom = self::get('contact_bg');
        return !empty($custom) ? $custom : asset(ltrim(self::DEFAULT_CONTACT_BG, '/'));
    }

    /**
     * Helper to split app name for two-tone Lamborghini styling (e.g. AU - DRIVE).
     */
    public static function getNamePart(int $part = 1): string
    {
        $name = trim(self::getName());
        if (mb_strlen($name) <= 2) {
            return $part === 1 ? $name : '';
        }

        if (str_contains($name, ' ')) {
            $words = explode(' ', $name, 2);
            return $part === 1 ? $words[0] : (' ' . $words[1]);
        }

        // Split single word (first 2 chars in part 1, rest in part 2)
        return $part === 1 ? mb_substr($name, 0, 2) : mb_substr($name, 2);
    }
}
