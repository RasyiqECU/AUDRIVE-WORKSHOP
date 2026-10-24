<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SparePartCategory;
use Illuminate\Database\Seeder;

class SparePartCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mesin & Performa',
                'slug' => 'mesin-performa',
                'description' => 'Komponen blok mesin, piston, camshaft, timing belt, turbocharger, intercooler, dan gasket performa tinggi.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Sistem Pengereman',
                'slug' => 'sistem-pengereman',
                'description' => 'Kampas rem performa, piringan cakram keramik/karbon, kaliper rem, selang rem braided, dan minyak rem sport.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Suspensi & Kaki-Kaki',
                'slug' => 'suspensi-kaki-kaki',
                'description' => 'Coilover, shockbreaker adjustable, lowering springs, sway bar, control arm, bushing polyurethane, dan tie rod.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Transmisi & Drivetrain',
                'slug' => 'transmisi-drivetrain',
                'description' => 'Kopling dual-clutch, flywheel ringan, gardan limited-slip differential (LSD), driveshaft, dan oli transmisi performa.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Kelistrikan & ECU',
                'slug' => 'kelistrikan-ecu',
                'description' => 'Modul ECU, sensor mesin & ABS, koil pengapian high-output, busi iridium, alternator, dan aki lithium sport.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Sistem Pendingin',
                'slug' => 'sistem-pendingin',
                'description' => 'Radiator aluminium, oil cooler, intercooler, thermostat balap, selang silikon, dan water pump high-flow.',
                'sort_order' => 6,
            ],
            [
                'name' => 'Sistem Pembuangan (Exhaust)',
                'slug' => 'sistem-pembuangan',
                'description' => 'Header exhaust stainless, downpipe, high-flow catalytic converter, valvetronic muffler, dan knalpot titanium.',
                'sort_order' => 7,
            ],
            [
                'name' => 'Filter & Pelumas',
                'slug' => 'filter-pelumas',
                'description' => 'Filter oli performa, filter udara replacement / cold air intake, filter bensin, dan oli mesin full synthetic.',
                'sort_order' => 8,
            ],
            [
                'name' => 'Body & Aerodinamika',
                'slug' => 'body-aerodinamika',
                'description' => 'Bumper serat karbon, front lip, rear diffuser, side skirt, rear wing spoiler, kap mesin berventilasi, dan spion karbon.',
                'sort_order' => 9,
            ],
            [
                'name' => 'Interior & Aksesoris',
                'slug' => 'interior-aksesoris',
                'description' => 'Jok bucket seat, sabuk harness 4-titik, setir alcantara/karbon, paddle shifter custom, dan roll bar.',
                'sort_order' => 10,
            ],
            [
                'name' => 'Velg & Ban Performa',
                'slug' => 'velg-ban-performa',
                'description' => 'Velg forged ultra-ringan, center-lock wheels, baut titanium, dan ban performa tinggi semi-slick.',
                'sort_order' => 11,
            ],
            [
                'name' => 'Bahan Bakar & Induksi Udara',
                'slug' => 'bahan-bakar-induksi',
                'description' => 'Injector bahan bakar high-flow, fuel pump tekanan tinggi, fuel pressure regulator, dan intake manifold.',
                'sort_order' => 12,
            ],
        ];

        foreach ($categories as $cat) {
            SparePartCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
