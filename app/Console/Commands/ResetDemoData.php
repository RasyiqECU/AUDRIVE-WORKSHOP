<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetDemoData extends Command
{
    protected $signature = 'demo:reset {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Hapus akun dan transaksi (katalog mobil & spare part disimpan), lalu isi data demo Jan 2025 - sekarang';

    /** Tabel yang tidak dikosongkan. */
    private const KEEP = [
        'migrations', 'car_units', 'car_photos', 'spare_parts', 'spare_part_categories',
        'spare_part_compatibilities', 'settings',
    ];

    public function handle(): int
    {
        if ($this->laravel->isProduction()) {
            $this->error('Perintah ini tidak boleh dijalankan di production.');

            return self::FAILURE;
        }

        $database = DB::getDatabaseName();
        if (! $this->option('force') && ! $this->confirm("Semua akun dan transaksi di database \"{$database}\" akan DIHAPUS. Lanjutkan?")) {
            return self::FAILURE;
        }

        // TRUNCATE sekaligus mengembalikan AUTO_INCREMENT ke 1
        Schema::withoutForeignKeyConstraints(function () {
            foreach (Schema::getTableListing(schema: DB::getDatabaseName(), schemaQualified: false) as $table) {
                if (! in_array($table, self::KEEP, true)) {
                    DB::table($table)->truncate();
                }
            }
        });

        DB::table('car_units')->update(['seller_customer_id' => null, 'price_approved_by' => null, 'seller_paid_by' => null]);
        DB::table('spare_parts')->update(['booked_stock' => 0]);

        Storage::disk('public')->deleteDirectory('ai-scans');
        Storage::disk('local')->deleteDirectory('payment-proofs');

        $this->call('db:seed', ['--class' => DemoDataSeeder::class, '--force' => true]);
        $this->info("Selesai. Database \"{$database}\" berisi data demo; semua password: password");

        return self::SUCCESS;
    }
}
