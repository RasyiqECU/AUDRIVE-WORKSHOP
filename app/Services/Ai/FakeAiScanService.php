<?php


declare(strict_types=1);


namespace App\Services\Ai;


use App\Models\AiScan;
use App\Services\Ai\Contracts\AiScanServiceContract;
use Illuminate\Support\Facades\Log;


class FakeAiScanService implements AiScanServiceContract
{
    /**
     * {@inheritdoc}
     */
    public function analyze(string $audioPath): array
    {
        Log::info('Fake AI Service: Analyzing audio', ['audio_path' => $audioPath]);


        // Simulate processing time
        sleep(1);


        // Generate fake results
        $classes = [
            'engine_knocking' => [
                'recommendations' => [
                    'Periksa busi dan ganti jika diperlukan',
                    'Cek tekanan kompresi mesin',
                    'Gunakan bahan bakar dengan oktan lebih tinggi',
                    'Bawa ke bengkel untuk inspeksi lebih lanjut',
                ],
            ],
            'bearing_abnormal' => [
                'recommendations' => [
                    'Segera bawa ke bengkel untuk pemeriksaan bearing',
                    'Hindari mengemudi dengan kecepatan tinggi',
                    'Periksa pelumasan bearing',
                    'Ganti bearing jika diperlukan',
                ],
            ],
            'belt_roller_worn' => [
                'recommendations' => [
                    'Ganti belt/roller yang aus',
                    'Periksa tensioner belt',
                    'Lakukan servis CVT',
                    'Gunakan spare part original',
                ],
            ],
            'exhaust_leak' => [
                'recommendations' => [
                    'Periksa sistem knalpot untuk kebocoran',
                    'Ganti gasket knalpot jika diperlukan',
                    'Periksa manifold knalpot',
                    'Lakukan pengelasan jika ada retakan',
                ],
            ],
            'idle_unstable' => [
                'recommendations' => [
                    'Bersihkan throttle body',
                    'Periksa sensor IAC (Idle Air Control)',
                    'Cek vacuum hose untuk kebocoran',
                    'Reset ECU jika diperlukan',
                ],
            ],
            'transmission_noise' => [
                'recommendations' => [
                    'Periksa level dan kondisi oli transmisi',
                    'Bawa ke bengkel untuk inspeksi transmisi',
                    'Hindari mengemudi hingga diperbaiki',
                    'Ganti oli transmisi jika diperlukan',
                ],
            ],
        ];


        // Randomly select a class
        $predictedClass = array_rand($classes);
        $classData = $classes[$predictedClass];


        // Generate random confidence between 0.75 and 0.95
        $confidence = mt_rand(75, 95) / 100;


        return [
            'predicted_class' => $predictedClass,
            'confidence' => $confidence,
            'recommendations' => $classData['recommendations'],
            'spectrogram_url' => null, // Fake service doesn't generate spectrograms
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function processScan(AiScan $scan): void
    {
        try {
            $scan->update(['status' => 'processing']);


            $result = $this->analyze($scan->audio_path);


            $scan->update([
                'status' => 'completed',
                'predicted_class' => $result['predicted_class'],
                'confidence' => $result['confidence'],
                'recommendations' => $result['recommendations'],
                'spectrogram_path' => $result['spectrogram_url'],
            ]);


            Log::info('Fake AI Service: Scan completed', [
                'scan_id' => $scan->id,
                'predicted_class' => $result['predicted_class'],
                'confidence' => $result['confidence'],
            ]);


        } catch (\Exception $e) {
            $scan->update([
                'status' => 'failed',
                'recommendations' => ['Error processing scan: ' . $e->getMessage()],
            ]);


            Log::error('Fake AI Service: Scan failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}



