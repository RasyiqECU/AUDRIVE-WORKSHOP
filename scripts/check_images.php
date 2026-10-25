<?php
// Verifikasi sementara: cocokkan path foto di DB dengan file fisik (BACA-ONLY)
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$checks = [];

// CarPhoto (foto mobil unit)
$photos = \App\Models\CarPhoto::select('id', 'path')->limit(20)->get();
foreach ($photos as $p) {
    $field = $p->path;
    $checks[] = ['table' => 'car_photos', 'id' => $p->id, 'path' => $field, 'exists' => $field ? \Storage::disk('public')->exists($field) : false];
}

// CarUnit (photo utama?)
if (\Schema::hasTable('car_units')) {
    $cols = \Schema::getColumnListing('car_units');
    $photoCols = array_values(array_filter($cols, fn ($c) => str_contains($c, 'photo') || str_contains($c, 'image')));
    $units = \App\Models\CarUnit::select(array_merge(['id'], $photoCols))->limit(20)->get();
    foreach ($units as $u) {
        foreach ($photoCols as $col) {
            $v = $u->{$col};
            $checks[] = ['table' => 'car_units.' . $col, 'id' => $u->id, 'path' => $v, 'exists' => $v ? \Storage::disk('public')->exists($v) : false];
        }
    }
}

// AiScan spectrogram
$scans = \App\Models\AiScan::select('id', 'spectrogram_path', 'audio_path')->limit(10)->get();
foreach ($scans as $s) {
    foreach (['spectrogram_path', 'audio_path'] as $col) {
        $v = $s->{$col};
        $checks[] = ['table' => 'ai_scans.' . $col, 'id' => $s->id, 'path' => $v, 'exists' => $v ? \Storage::disk('public')->exists($v) : false];
    }
}

$missing = 0;
foreach ($checks as $c) {
    $status = $c['exists'] ? 'OK  ' : 'MISS';
    if (!$c['exists']) { $missing++; }
    echo sprintf("%s [%s #%s] %s\n", $status, $c['table'], $c['id'], $c['path'] ?? '(null)');
}
echo "TOTAL: " . count($checks) . " | MAU: $missing\n";
