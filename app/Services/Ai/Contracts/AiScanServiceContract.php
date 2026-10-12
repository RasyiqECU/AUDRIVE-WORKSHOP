<?php


declare(strict_types=1);


namespace App\Services\Ai\Contracts;


use App\Models\AiScan;


interface AiScanServiceContract
{
    /**
     * Analyze audio file using AI model
     *
     * @param string $audioPath Path to audio file
     * @return array{
     *   predicted_class: string,
     *   confidence: float,
     *   recommendations: array<string>,
     *   spectrogram_url: string|null
     * }
     */
    public function analyze(string $audioPath): array;


    /**
     * Process scan asynchronously
     */
    public function processScan(AiScan $scan): void;
}



