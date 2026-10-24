<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\CarUnit;
use App\Models\SparePartCategory;
use Illuminate\Support\Facades\Cache;


class CacheService
{
    /**
     * Get featured cars with caching
     */
    public function getFeaturedCars(int $limit = 6): \Illuminate\Support\Collection
    {
        return Cache::remember('homepage_featured_cars', config('cache.ttl.homepage_featured_cars'), function () use ($limit) {
            return CarUnit::query()
                ->where('status', 'available')
                ->whereNotNull('featured_at')
                ->with('primaryPhoto')
                ->orderByDesc('featured_at')
                ->limit($limit)
                ->get();
        });
    }


    /**
     * Get spare part categories with caching
     */
    public function getSparePartCategories(): \Illuminate\Support\Collection
    {
        return Cache::remember('spare_part_categories', config('cache.ttl.spare_part_categories'), function () {
            return SparePartCategory::query()
                ->orderBy('name')
                ->get();
        });
    }


    /**
     * Get car brands with caching
     */
    public function getCarBrands(): \Illuminate\Support\Collection
    {
        return Cache::remember('car_brands', config('cache.ttl.car_brands'), function () {
            return CarUnit::query()
                ->select('brand')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand');
        });
    }


    /**
     * Clear featured cars cache
     */
    public function clearFeaturedCarsCache(): void
    {
        Cache::forget('homepage_featured_cars');
    }


    /**
     * Clear spare part categories cache
     */
    public function clearSparePartCategoriesCache(): void
    {
        Cache::forget('spare_part_categories');
    }


    /**
     * Clear car brands cache
     */
    public function clearCarBrandsCache(): void
    {
        Cache::forget('car_brands');
    }


    /**
     * Clear all caches
     */
    public function clearAllCaches(): void
    {
        $this->clearFeaturedCarsCache();
        $this->clearSparePartCategoriesCache();
        $this->clearCarBrandsCache();
    }
}



